<?php

declare(strict_types=1);

namespace App\Override;

use App\Config\Config;
use App\File\FileCacheService;
use App\Parser\DocParser;
use App\Parser\FileParser;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Finder\SplFileInfo;

class ClassLoader
{
    private FileParser $fileParser;
    private FileCacheService $fileCacheService;

    public function __construct(Config $config)
    {
        $this->fileParser = new FileParser($config);
        $this->fileCacheService = new FileCacheService($config);
    }

    public function load(): void
    {
        $cache = $this->fileCacheService->readFromCache();


        spl_autoload_register(
            function (string $className) use ($cache) {
                if (array_key_exists($className, $cache->toArray()) === false) {
                    return;
                }

                include $cache->toArray()[$className];
            },
            true,
            true
        );

        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @param \Iterator<SplFileInfo> $files
     * @throws \ReflectionException
     */
    public function parseAllFiles(\Iterator $files): void
    {
        $cache = $this->fileCacheService->readFromCache();

        foreach ($files as $file) {
            $namespace = $this->fileParser->getNamespace($file->getPathname());

            if ($namespace === null) {
                continue;
            }

            $annotations = (new DocParser())->parse($namespace);

            if ($annotations->isEmpty()) {
                continue;
            }

            $resultFile = $this->fileParser->parse($file, $annotations);

            $cache = $cache->add($namespace, $resultFile);
        }

        $this->fileCacheService->saveToCache($cache);
    }
}