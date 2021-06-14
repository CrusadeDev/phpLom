<?php

declare(strict_types=1);

namespace App\File;

use App\Builder\FilePathBuilder;
use App\Config\Config;
use App\ValueObject\FileContent;
use App\ValueObject\FileName;
use App\ValueObject\Path;
use Symfony\Component\Filesystem\Filesystem;

class OverrideFileService
{
    private Filesystem $fileSystem;
    private FilePathBuilder $fileBuilder;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->fileSystem = new Filesystem();
        $this->fileBuilder = new FilePathBuilder();
        $this->config = $config;
    }

    public function save(FileName $name, FileContent $content): void
    {
        $this->fileSystem->dumpFile((string)$this->getFilePath($name), $content);
    }

    private function getFilePath(FileName $name): Path
    {
        return $this->fileBuilder->buildFileName($this->config->getCachePath(), $name);
    }
}