<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\ValueObject;

class FileNamespace
{
    private ?string $namespace;

    public function __construct(?string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function getNamespace(): string
    {
        if ($this->hasNamespace() === false) {
            throw new \LogicException('Namespace not found');
        }

        return $this->namespace;
    }

    public function hasNamespace(): bool
    {
        return $this->namespace !== null;
    }
}