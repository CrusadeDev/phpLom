<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Factory;

use Crusade\PhpLom\Decorator\Interfaces\AnnotationInterface;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;

class PropertyDataFactory
{
    public function buildFromPropertyReflection(
        \ReflectionProperty $property,
        AnnotationInterface $annotation
    ): PropertyData {
        return new PropertyData($annotation, $property->getName(), $this->getType($property));
    }

    private function getType(\ReflectionProperty $property): ?string
    {
        if ($property->hasType() === false) {
            return null;
        }

        return $property->getType()->getName();
    }
}