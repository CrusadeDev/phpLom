<?php

declare(strict_types=1);

namespace Crusade\PhpLom\File;

use Crusade\PhpLom\ValueObject\FileContent;
use Crusade\PhpLom\ValueObject\Path;

class FileReader
{
    /**
     * @throws \RuntimeException
     */
    public function readFile(Path $filePath): FileContent
    {
        if (file_exists((string)$filePath) === false) {
            throw new \RuntimeException('File does not exist');
        }

        $content = file_get_contents((string)$filePath);

        if ($content === false) {
            throw new \RuntimeException('Cannot open or read file');
        }

        return new FileContent($content);
    }
}