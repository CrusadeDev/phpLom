<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\ValueObject;

use PhpParser\Node\Stmt\Namespace_;

class FileNamespace
{
    private ?Namespace_ $namespace;

    public function __construct(?Namespace_ $namespace)
    {
        $this->namespace = $namespace;
    }

    public function getNamespace(): string
    {
        return $this->namespace->name->toString();
    }

    public function hasNamespace(): bool
    {
        return $this->namespace !== null;
    }
}