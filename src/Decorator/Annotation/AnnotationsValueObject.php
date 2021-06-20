<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Annotation;

use Illuminate\Support\Collection;

class AnnotationsValueObject
{
    public static function getPropertyAnnotations(): Collection
    {
        return new Collection(
            [
                Getter::class,
                Setter::class,
            ]
        );
    }

    public static function getClassAnnotations(): Collection
    {
        return new Collection(
            [
                Getter::class,
                Setter::class,
                ToString::class
            ]
        );
    }
}