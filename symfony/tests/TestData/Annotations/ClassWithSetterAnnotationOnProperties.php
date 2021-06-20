<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\TestData\Annotations;

use Crusade\PhpLom\Decorator\Annotation\Setter;

class ClassWithSetterAnnotationOnProperties
{
    /**
     * @Setter
     */
    private string $property;
}