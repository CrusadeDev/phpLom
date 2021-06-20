<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Parser;

use Crusade\PhpLom\Builder\AnnotationMethodBuilder;
use Crusade\PhpLom\Builder\PhpDocBuilder;
use Crusade\PhpLom\Config\Config;
use Crusade\PhpLom\File\OverrideFileService;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
use Crusade\PhpLom\ValueObject\FileContent;
use Crusade\PhpLom\ValueObject\FileName;
use Crusade\PhpLom\ValueObject\GeneratedMethodData;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Finder\SplFileInfo;

class FileParser
{
    private Parser $parser;
    private AnnotationMethodBuilder $builder;
    private OverrideFileService $overrideFileService;
    private Config $config;
    private PhpDocBuilder $docBuilder;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->builder = new AnnotationMethodBuilder();
        $this->overrideFileService = new OverrideFileService($config);
        $this->docBuilder = new PhpDocBuilder();
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
                        function (PropertyData $ann) use (&$generated) {
                            $stmt = $this->builder->buildForAnnotation($ann);

                            $generated->add(new GeneratedMethodData($ann, $stmt->name->toString()));

                            return $stmt;
                        }
                    )
                    ->each(fn(Stmt $stmt) => $class->stmts[] = $stmt);
            }
        );

        $phpDoc = $this->docBuilder->buildForGeneratedMethods($generated);

        $prettyPrinter = new Standard;
        $code1 = $prettyPrinter->prettyPrintFile($ast->all());
        $this->overrideFileService->save(FileName::fromString($filePath->getFilename()), new FileContent($code1));

        $ast2 = new Collection((new ParserFactory)->create(ParserFactory::PREFER_PHP7)->parse($code));
        $namespace = $this->findNamespace($ast2);
        $class = $this->findClasses($namespace);

        $class->setDocComment($phpDoc->toDoc());

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
}