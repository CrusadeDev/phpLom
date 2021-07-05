<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Builder;

use Crusade\PhpLom\Decorator\Annotation\Getter;
use Crusade\PhpLom\Decorator\Annotation\Setter;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;

class FunctionNameBuilder
{
    public function fromPropertyData(PropertyData $propertyData): string
    {
        if ($propertyData->getAnnotation() instanceof Getter) {
            return $this->buildGetterFunctionName($propertyData);
        }

        if ($propertyData->getAnnotation() instanceof Setter) {
            return $this->buildSetterFunctionName($propertyData);
        }

        throw new \LogicException('Unsupported annotation');
    }

    private function buildGetterFunctionName(PropertyData $propertyData): string
    {
        $name = ucfirst($propertyData->getPropertyName());

        return "get$name";
    }

    private function buildSetterFunctionName(PropertyData $propertyData): string
    {
        $name = ucfirst($propertyData->getPropertyName());

        return "set$name";
    }
}