<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Factory;

use Crusade\PhpLom\Builder\FunctionNameBuilder;
use Crusade\PhpLom\Decorator\Annotation\Getter;
use Crusade\PhpLom\Decorator\Annotation\Setter;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
use Crusade\PhpLom\ValueObject\PhpDoc;

class PhpDocFactory
{
    private FunctionNameBuilder $functionNameBuilder;

    public function __construct()
    {
        $this->functionNameBuilder = new FunctionNameBuilder();
    }

    public function buildForGeneratedMethod(PropertyData $propertyData): PhpDoc
    {
        if ($propertyData->getAnnotation() instanceof Getter) {
            return $this->getter($propertyData);
        }

        if ($propertyData->getAnnotation() instanceof Setter) {
            return $this->setter($propertyData);
        }

        throw new \LogicException('Unsupported Annotation');
    }

    private function getter(PropertyData $propertyData): PhpDoc
    {
        $type = $propertyData->getPropertyType();
        $name = $this->functionNameBuilder->fromPropertyData($propertyData);

        return new PhpDoc(" * @method $type $name()\n  ");
    }

    private function setter(PropertyData $propertyData): PhpDoc
    {
        $type = $propertyData->getPropertyType();
        $propertyName = $propertyData->getPropertyName();
        $name = $this->functionNameBuilder->fromPropertyData($propertyData);


        return new PhpDoc("* @method void $name($type \$$propertyName)\n  ");
    }
}