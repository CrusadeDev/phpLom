<?php

declare(strict_types=1);

namespace App\Parser;

use App\Config\Config;
use App\Factory\GetterFactory;
use App\File\OverrideFileService;
use App\Nodes\Annotation;
use App\ValueObject\FileContent;
use App\ValueObject\FileName;
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
    private GetterFactory $builder;

    public function __construct(Config $config)
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->builder = new GetterFactory();
        $this->overrideFileService = new OverrideFileService($config);
    }

    public function parse(SplFileInfo $filePath, Collection $annotations): ?string
    {
        $resultFile = "/var/www/symfony/var/Override/{$filePath->getFilename()}";

        $code = file_get_contents($filePath->getPathname());
        $ast = new Collection($this->parser->parse($code));
        $namespace = $this->findNamespace($ast);
        $class = $this->findClasses($namespace);

        if ($class === null) {
            return null;
        }

        $annotations->each(fn(Annotation $annotation) => $class->stmts[] = $this->builder->build($annotation));

        $prettyPrinter = new Standard;
        $code = $prettyPrinter->prettyPrintFile($ast->all());
        $this->overrideFileService->save(FileName::fromString($filePath->getFilename()), new FileContent($code));

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