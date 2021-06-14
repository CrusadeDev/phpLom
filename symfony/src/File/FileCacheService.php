<?php

/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

declare(strict_types=1);

namespace App\File;

use App\Builder\FilePathBuilder;
use App\Config\Config;
use App\ValueObject\CacheContent;
use App\ValueObject\FileName;
use App\ValueObject\Path;
use Symfony\Component\Filesystem\Filesystem;

class FileCacheService
{
    private Filesystem $fileSystem;
    private FilePathBuilder $fileBuilder;
    private FileReader $fileReader;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->fileSystem = new Filesystem();
        $this->fileBuilder = new FilePathBuilder();
        $this->fileReader = new FileReader();
        $this->config = $config;
    }

    public function readFromCache(): CacheContent
    {
        try {
            $content = $this->fileReader->readFile($this->getFilePath());
        } catch (\RuntimeException $exception) {
            return new CacheContent([]);
        }

        try {
            $contentArray = json_decode((string)$content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \LogicException($e->getMessage());
        }

        return new CacheContent($contentArray);
    }

    /**
     * @throws \JsonException
     */
    public function saveToCache(CacheContent $fileContent): void
    {
        $this->fileSystem->dumpFile(
            (string)$this->getFilePath(),
            json_encode($fileContent->toArray(), JSON_THROW_ON_ERROR)
        );
    }

    private function getFilePath(): Path
    {
        return $this->fileBuilder->buildFileName($this->config->getCachePath(), FileName::overrideCache());
    }
}