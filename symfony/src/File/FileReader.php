<?php

declare(strict_types=1);

namespace App\File;

use App\ValueObject\FileContent;
use App\ValueObject\Path;

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