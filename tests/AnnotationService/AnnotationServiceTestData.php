<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\AnnotationService;

use Crusade\PhpLom\Decorator\Annotation\Getter;
use Crusade\PhpLom\Decorator\Annotation\ToString;
use Crusade\PhpLom\Decorator\ValueObject\ClassData;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
use Crusade\PhpLom\Decorator\Annotation\Setter;

class AnnotationServiceTestData
{
    public function getExpectedGetterPropertyData(): PropertyData
    {
        return new PropertyData(new Getter(), 'property', 'string');
    }

    public function getExpectedSetterPropertyData(): PropertyData
    {
        return new PropertyData(new Setter(), 'property', 'string');
    }

    public function getExpectedMixedProperties(): array
    {
        return [
            new PropertyData(new Setter(), 'propertyOne', 'string'),
            new PropertyData(new Getter(), 'propertyTwo', 'string'),
        ];
    }

    public function getExpectedGetterWithoutType(): PropertyData
    {
        return new PropertyData(new Getter(), 'property', null);
    }

    public function getNonPropertyAnnotation(): ClassData
    {
        return new ClassData(new ToString());
    }
}