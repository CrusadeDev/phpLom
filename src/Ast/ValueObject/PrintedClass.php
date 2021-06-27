<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\ValueObject;

class PrintedClass
{
    private string $printedClass;

    public function __construct(string $printedClass)
    {
        $this->printedClass = $printedClass;
    }

    public function __toString(): string
    {
        return $this->printedClass;
    }
}