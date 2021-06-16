<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Builder;

use Crusade\PhpLom\ValueObject\FileName;
use Crusade\PhpLom\ValueObject\Path;

class FilePathBuilder
{
    public function buildFileName(Path $path, FileName $fileName): Path
    {
        return new Path("$path/$fileName");
    }
}