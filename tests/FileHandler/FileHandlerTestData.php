<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\FileHandler;

use Crusade\PhpLom\Config\Config;

class FileHandlerTestData
{
    public function getConfig(): Config
    {
        return new Config('test', 'test', true);
    }
}