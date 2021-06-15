<?php

declare(strict_types=1);

namespace App\Parser;

use App\Builder\AnnotationMethodBuilder;
use App\Config\Config;
use App\File\OverrideFileService;
use App\Nodes\Annotation;
use App\Nodes\Getter;
use App\Nodes\Setter;
use App\ValueObject\FileContent;
use App\ValueObject\FileName;
use Illuminate\Support\Collection;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @method int dead(string)
 */
class FileParser
{
    private Parser $parser;
    private AnnotationMethodBuilder $builder;
    private OverrideFileService $overrideFileService;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->builder = new AnnotationMethodBuilder();
        $this->overrideFileService = new OverrideFileService($config);
    }

    public function parse(SplFileInfo $filePath, Collection $annotations): ?string
    {
        $resultFile = $this->config->getCachePath().'/'.$filePath->getFilename();

        $code = file_get_contents($filePath->getPathname());
        $ast = new Collection($this->parser->parse($code));
        $namespace = $this->findNamespace($ast);
        $class = $this->findClasses($namespace);

        if ($class === null) {
            return null;
        }

        $generated = new Collection([]);

        $annotations->each(
            function (Collection $annotation) use ($class, &$generated) {
                $annotation
                    ->transform(
                        function (Annotation $ann) use (&$generated) {
                            $stmt = $this->builder->buildForAnnotation($ann);

                            $generated->add(['stmt' => $stmt, 'ann' => $ann]);

                            return $stmt;
                        }
                    )
                    ->tap(fn(Collection $collection) => $generated = $collection)
                    ->each(fn(Stmt $stmt) => $class->stmts[] = $stmt);
            }
        );

        $doc = $generated->transform(fn(array $stmt) => $this->getMethodDocDoc($stmt));

        $s = "/** \n";
        $doc->each(
            function (string $d) use (&$s) {
                return $s .= $d;
            }
        );
        $s .= '*/';
        $doc = new Doc($s);

        $prettyPrinter = new Standard;
        $code1 = $prettyPrinter->prettyPrintFile($ast->all());
        $this->overrideFileService->save(FileName::fromString($filePath->getFilename()), new FileContent($code1));

        $ast2 = new Collection((new ParserFactory)->create(ParserFactory::PREFER_PHP7)->parse($code));
        $namespace = $this->findNamespace($ast2);
        $class = $this->findClasses($namespace);

        $class->setDocComment($doc);

        $this->overrideFileService->addDoc(
            $filePath->getPathname(),
            new FileContent($prettyPrinter->prettyPrintFile($ast2->all()))
        );

        return $resultFile;
    }

    /**
     * @param Collection<Stmt> $stmts
     * @return Namespace_
     */
    private function findNamespace(Collection $stmts): Namespace_
    {
        return $stmts->filter(fn(Stmt $stmt) => get_class($stmt) === Namespace_::class)->pop();
    }

    /**
     * @param Namespace_ $namespace
     * @return Stmt\Class_
     */
    private function findClasses(Namespace_ $namespace): ?Stmt\Class_
    {
        return (new Collection($namespace->stmts))
            ->filter(fn(Stmt $stmt) => get_class($stmt) === Stmt\Class_::class)
            ->pop();
    }

    public function getNamespace(string $filePath): ?string
    {
        $code = file_get_contents($filePath);
        $ast = new Collection($this->parser->parse($code));

        $namespace = $this->findNamespace($ast);
        $class = $this->findClasses($namespace);

        if ($class === null) {
            return null;
        }

        $className = $class->name;

        $namespaceString = $namespace->name->toCodeString();

        return "$namespaceString\\$className";
    }

    private function getMethodDocDoc(array $stmt): string
    {
        $ann = $stmt['ann'];

        $type = $stmt['ann']->getPropertyType();
        $name = $stmt['stmt']->name->toString();

        if ($ann->getAnnotation() instanceof Setter) {
            return " * @method void $name($type \${$ann->getPropertyName()})\n  ";
        }

        if ($ann->getAnnotation() instanceof Getter) {
            return " * @method $type $name()\n  ";
        }

        return '';
    }
}