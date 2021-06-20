<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\TestData\Annotations;

use Crusade\PhpLom\Decorator\Annotation\Getter;

class ClassWithGetterAnnotationOnProperties
{
    /**
     * @Getter
     */
    private string $property;
}