<?php

declare(strict_types=1);

namespace App\Override;

use Symfony\Component\Filesystem\Filesystem;

class OverrideConfig
{
    private Filesystem $fileSystem;

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }

    /**
     * @throws \JsonException
     */
    public function appendToCache(string $namespace, string $resultFile): void
    {
        $filePath = "/var/www/symfony/var/Override/result.json";

        if ($this->fileSystem->exists($filePath) === false) {
            $this->fileSystem->dumpFile($filePath, '{}');
        }

        $content = file_get_contents($filePath);
        $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        $json[$namespace] = $resultFile;

        $this->fileSystem->dumpFile($filePath, json_encode($json, JSON_THROW_ON_ERROR));
    }

}