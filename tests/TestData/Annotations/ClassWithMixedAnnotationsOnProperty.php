<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\TestData\Annotations;

use Crusade\PhpLom\Decorator\Annotation\Getter;
use Crusade\PhpLom\Decorator\Annotation\Setter;

class ClassWithMixedAnnotationsOnProperty
{
    /**
     * @Setter
     */
    private string $propertyOne;

    /**
     * @Getter
     */
    private string $propertyTwo;
}