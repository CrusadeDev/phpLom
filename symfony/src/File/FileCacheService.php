<?php

/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

declare(strict_types=1);

namespace App\File;

use App\Builder\FilePathBuilder;
use App\Config\Config;
use App\Parser\JsonParser;
use App\ValueObject\CacheContent;
use App\ValueObject\FileName;
use App\ValueObject\JsonContent;
use App\ValueObject\Path;
use Symfony\Component\Filesystem\Filesystem;

class FileCacheService
{
    private Filesystem $fileSystem;
    private FilePathBuilder $fileBuilder;
    private FileReader $fileReader;
    private Config $config;
    private JsonParser $parser;

    public function __construct(Config $config)
    {
        $this->fileSystem = new Filesystem();
        $this->fileBuilder = new FilePathBuilder();
        $this->fileReader = new FileReader();
        $this->parser = new JsonParser();
        $this->config = $config;
    }

    public function readFromCache(): CacheContent
    {
        try {
            $content = (string)$this->fileReader->readFile($this->getFilePath());
        } catch (\RuntimeException $exception) {
            return new CacheContent([]);
        }

        return new CacheContent($this->parser->decode(new JsonContent($content)));
    }

    public function saveToCache(CacheContent $fileContent): void
    {
        $this->fileSystem->dumpFile(
            (string)$this->getFilePath(),
            (string)$this->parser->encode($fileContent->toArray())
        );
    }

    private function getFilePath(): Path
    {
        return $this->fileBuilder->buildFileName($this->config->getCachePath(), FileName::overrideCache());
    }
}