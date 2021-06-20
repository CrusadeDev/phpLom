<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Interfaces;

interface AnnotationInterface
{
    public function hasClassAlias(): bool;
}