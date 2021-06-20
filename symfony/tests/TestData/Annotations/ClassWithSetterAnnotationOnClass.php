<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\TestData\Annotations;

use Crusade\PhpLom\Decorator\Annotation\Setter;

/**
 * @Setter
 */
class ClassWithSetterAnnotationOnClass
{
    private string $property;
}