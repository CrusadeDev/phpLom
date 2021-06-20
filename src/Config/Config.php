<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Config;

use Crusade\PhpLom\ValueObject\Path;

class Config
{
    private Path $cachePath;
    private Path $filesPath;
    private bool $developerMode;

    public function __construct(string $cachePath, string $filesPath, bool $developerMode)
    {
        $this->cachePath = new Path($cachePath);
        $this->filesPath = new Path($filesPath);
        $this->developerMode = $developerMode;
    }

    public function getCachePath(): Path
    {
        return $this->cachePath;
    }

    public function getFilesPath(): Path
    {
        return $this->filesPath;
    }

    public function isDeveloperMode(): bool
    {
        return $this->developerMode;
    }
}