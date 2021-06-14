<?php

declare(strict_types=1);

namespace App\Builder;

use App\ValueObject\FileName;
use App\ValueObject\Path;

class FilePathBuilder
{
    public function buildFileName(Path $path, FileName $fileName): Path
    {
        return new Path("$path/$fileName");
    }
}