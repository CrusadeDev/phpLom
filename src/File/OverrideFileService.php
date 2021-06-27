<?php

declare(strict_types=1);

namespace Crusade\PhpLom\File;

use Crusade\PhpLom\Ast\ValueObject\PrintedClass;
use Crusade\PhpLom\Builder\FilePathBuilder;
use Crusade\PhpLom\Config\Config;
use Crusade\PhpLom\ValueObject\FileContent;
use Crusade\PhpLom\ValueObject\FileName;
use Crusade\PhpLom\ValueObject\Path;
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

    public function save(FileName $name, PrintedClass $content): string
    {
        $path = (string)$this->getFilePath($name);
        $this->fileSystem->dumpFile($path, $content);

        return $path;
    }

    public function addDoc(string $file, FileContent $content): void
    {
        $this->fileSystem->dumpFile($file, $content);

    }

    private function getFilePath(FileName $name): Path
    {
        return $this->fileBuilder->buildFileName($this->config->getCachePath(), $name);
    }
}