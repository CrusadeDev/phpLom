<?php

/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

declare(strict_types=1);

namespace Crusade\PhpLom\File;

use Crusade\PhpLom\Builder\FilePathBuilder;
use Crusade\PhpLom\Config\Config;
use Crusade\PhpLom\Parser\JsonParser;
use Crusade\PhpLom\ValueObject\CacheContent;
use Crusade\PhpLom\ValueObject\FileName;
use Crusade\PhpLom\ValueObject\JsonContent;
use Crusade\PhpLom\ValueObject\Path;
use Symfony\Component\Filesystem\Filesystem;

class FileCacheService
{
    private Filesystem $fileSystem;
    private FilePathBuilder $fileBuilder;
    private FileReader $fileReader;
    private Config $config;
    private JsonParser $parser;
    private ?CacheContent $cacheContent = null;


    public function __construct(Config $config)
    {
        $this->fileSystem = new Filesystem();
        $this->fileBuilder = new FilePathBuilder();
        $this->fileReader = new FileReader();
        $this->parser = new JsonParser();
        $this->config = $config;
    }

    public function readFromCache(FileName $fileName): CacheContent
    {
        if ($this->cacheContent !== null) {
            return $this->cacheContent;
        }

        try {
            $content = (string)$this->fileReader->readFile($this->getFilePath($fileName));
        } catch (\RuntimeException $exception) {
            return new CacheContent([]);
        }

        $cache = new CacheContent($this->parser->decode(new JsonContent($content)));

        $this->cacheContent = $cache;

        return $cache;
    }

    public function saveToCache(CacheContent $fileContent, FileName $fileName): void
    {
        $this->cacheContent = $fileContent;

        $this->fileSystem->dumpFile(
            (string)$this->getFilePath($fileName),
            (string)$this->parser->encode($fileContent->toArray())
        );
    }

    private function getFilePath(FileName $fileName): Path
    {
        return $this->fileBuilder->buildFileName($this->config->getCachePath(), $fileName);
    }
}