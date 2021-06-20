<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Annotation;

use Crusade\PhpLom\Decorator\Interfaces\AnnotationInterface;

/**
 * @Annotation
 */
class ToString implements AnnotationInterface
{
    public function hasClassAlias(): bool
    {
        return false;
    }
}