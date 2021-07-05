<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\ValueObject;

use PhpParser\Node\Stmt\Class_;

class FileClass
{
    private ?Class_ $class;

    public function __construct(?Class_ $class)
    {
        $this->class = $class;
    }

    public function getClass(): Class_
    {
        if ($this->hasNamespace() === false) {
            throw new \LogicException('Class not found');
        }

        return $this->class;
    }

    public function hasNamespace(): bool
    {
        return $this->class !== null;
    }
}