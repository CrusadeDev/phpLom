<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\ValueObject;

use Crusade\PhpLom\ValueObject\Path;

class ParsedFile
{
    private array $ast;
    private Path $filePath;

    public function __construct(array $ast, Path $filePath)
    {
        $this->ast = $ast;
        $this->filePath = $filePath;
    }

    public function getAst(): array
    {
        return $this->ast;
    }

    public function getFilePath(): Path
    {
        return $this->filePath;
    }
}