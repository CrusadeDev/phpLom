<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Override;

use Crusade\PhpLom\Config\Config;
use Crusade\PhpLom\File\FileCacheService;
use Crusade\PhpLom\Parser\DocParser;
use Crusade\PhpLom\Parser\FileParser;
use Crusade\PhpLom\ValueObject\FileName;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ClassLoader
{
    private FileParser $fileParser;
    private FileCacheService $fileCacheService;
    private Config $config;
    private DocParser $docParser;

    public function __construct(Config $config)
    {
        $this->fileParser = new FileParser($config);
        $this->fileCacheService = new FileCacheService($config);
        $this->config = $config;
        $this->docParser = new DocParser();
    }

    /**
     * @throws \ReflectionException
     */
    public function load(): void
    {
        $cache = $this->fileCacheService->readFromCache(FileName::overrideCache());

        spl_autoload_register(
            function (string $className) use ($cache) {
                if (array_key_exists($className, $cache->toArray()) === false) {
                    return;
                }

                if (file_exists($cache->toArray()[$className]['result']) === false) {
                    return;
                }

                include $cache->toArray()[$className]['result'];
            },
            true,
            true
        );

        AnnotationRegistry::registerLoader('class_exists');

        if ($this->config->isDeveloperMode() === true) {

            $finder = new Finder();

            $files = $finder->files()->in((string)$this->config->getFilesPath())->depth('>0')->getIterator();

            $this->parseAllFiles($files);
        }
    }

    /**
     * @param \Iterator<SplFileInfo> $files
     * @throws \ReflectionException
     */
    public function parseAllFiles(\Iterator $files): void
    {
        $cache = $this->fileCacheService->readFromCache(FileName::overrideCache());
        $fileCheckedCache = $this->fileCacheService->readFromCache(FileName::checkedFilesCache());

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $namespace = $this->fileParser->getNamespace($file->getPathname());

            $mtime = filemtime($file->getPath());

            if (
                array_key_exists($filePath, $fileCheckedCache->toArray())
                && (int)$fileCheckedCache->toArray()[$filePath]['time'] === $mtime
            ) {
                continue;
            }

            if ($namespace === null) {
                $fileCheckedCache = $fileCheckedCache->add($filePath, ['time' => $mtime]);
                continue;
            }

            $annotations = $this->docParser->parse($namespace);

            if ($annotations->isEmpty()) {
                $fileCheckedCache = $fileCheckedCache->add($filePath, ['time' => $mtime]);
                continue;
            }

            $resultFile = $this->fileParser->parse($file, $annotations);

            $cache = $cache->add($namespace, ['result' => $resultFile, 'time' => filemtime($file->getPath())]);
            $fileCheckedCache = $fileCheckedCache->add($filePath, ['time' => $mtime]);
        }

        $this->fileCacheService->saveToCache($cache, FileName::overrideCache());
        $this->fileCacheService->saveToCache($fileCheckedCache, FileName::checkedFilesCache());
    }
}