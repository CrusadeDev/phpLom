<?php

declare(strict_types=1);

namespace Crusade\PhpLom\ValueObject;

use PhpParser\Comment\Doc;

class PhpDoc
{
    private string $doc;

    public function __construct(string $doc)
    {
        $this->doc = $doc;
    }

    public function toDoc(): Doc
    {
        return new Doc($this->doc);
    }

    public function __toString(): string
    {
        return $this->doc;
    }
}