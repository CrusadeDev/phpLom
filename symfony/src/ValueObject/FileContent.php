<?php

declare(strict_types=1);

namespace Crusade\PhpLom\ValueObject;

class FileContent
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}