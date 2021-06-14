<?php

declare(strict_types=1);

namespace App\Config;

use App\ValueObject\Path;

class Config
{
    private Path $cachePath;

    public function __construct(string $cachePath)
    {
        $this->cachePath = new Path($cachePath);
    }

    public function getCachePath(): Path
    {
        return $this->cachePath;
    }
}