<?php

declare(strict_types=1);

namespace Crusade\PhpLom\ValueObject;

class Path
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function __toString(): string
    {
        return $this->path;
    }
}