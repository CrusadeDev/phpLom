<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Override;

use Crusade\PhpLom\Ast\Exceptions\FileDoesNotHaveClassException;
use Crusade\PhpLom\Ast\PhpFileParserFacade;
use Crusade\PhpLom\Ast\ValueObject\FileNamespace;
use Crusade\PhpLom\Ast\ValueObject\ParsedFile;
use Crusade\PhpLom\Builder\AnnotationMethodBuilder;
use Crusade\PhpLom\Builder\PhpDocBuilder;
use Crusade\PhpLom\Config\Config;
use Crusade\PhpLom\Decorator\DecoratorFacade;
use Crusade\PhpLom\Decorator\Interfaces\DecoratorDataInterface;
use Crusade\PhpLom\File\FileCacheService;
use Crusade\PhpLom\File\OverrideFileService;
use Crusade\PhpLom\ValueObject\FileName;
use Crusade\PhpLom\ValueObject\Path;
use Crusade\PhpLom\ValueObject\PhpDoc;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt\Class_;
use Symfony\Component\Finder\SplFileInfo;

class FileHandler
{
    private DecoratorFacade $decoratorFacade;
    private PhpFileParserFacade $parserFacade;
    private OverrideFileService $overrideFileService;
    private FileCacheService $cacheService;
    private AnnotationMethodBuilder $methodBuilder;
    private PhpDocBuilder $docBuilder;

    public function __construct(Config $config)
    {
        $this->decoratorFacade = new DecoratorFacade();
        $this->parserFacade = new PhpFileParserFacade();
        $this->overrideFileService = new OverrideFileService($config);
        $this->cacheService = new FileCacheService($config);
        $this->methodBuilder = new AnnotationMethodBuilder();
        $this->docBuilder = new PhpDocBuilder();
    }

    /**
     * @throws \ReflectionException
     * @throws FileDoesNotHaveClassException
     */
    public function handle(SplFileInfo $file): void
    {
        $ast = $this->parserFacade->parseFileToAst(new Path($file->getPathname()));

        $namespace = $this->parserFacade->getNamespace($ast);

        if ($namespace->hasNamespace() === false) {
            return;
        }

        if ($this->parserFacade->hasClass($ast) === false) {
            return;
        }

        $annotations = $this->decoratorFacade->readAnnotations($namespace->getNamespace());

        $this->generateClassWithMethods($annotations, $ast, $file, $namespace);
    }

    private function generatePhpDoc(Collection $generatedMethods, Class_ $class): PhpDoc
    {
        return $this->docBuilder->buildForGeneratedMethods($generatedMethods, $class);
    }

    /**
     * @throws FileDoesNotHaveClassException
     */
    private function generateClassWithMethods(
        Collection $annotations,
        ParsedFile $ast,
        SplFileInfo $file,
        FileNamespace $namespace
    ): void {
        $generatedMethods = $annotations->collect()->transform(
            fn(DecoratorDataInterface $decoratorData) => $this->methodBuilder->buildForAnnotation($decoratorData)
        );

        $astWithGeneratedMethods = $this->parserFacade->attachedGeneratedMethodsToClass($generatedMethods, $ast);

        $resultFile = $this->overrideFileService->save(
            FileName::fromString($file->getFilename()),
            $this->parserFacade->printClass($astWithGeneratedMethods)
        );

        $cache = $this->cacheService->readFromCache(FileName::overrideCache());

        $cache->add($namespace->getNamespace(), ['result' => $resultFile, 'time' => filemtime($file->getPath())]);
        $this->cacheService->saveToCache($cache, FileName::overrideCache());
    }

    private function addDocToExistingClass(ParsedFile $ast, Collection $annotations): void
    {
        $class = $this->parserFacade->getClass($ast);
        $doc = $this->generatePhpDoc($annotations, $class->getClass());

    }
}