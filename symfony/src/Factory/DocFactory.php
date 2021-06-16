<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Factory;

use Crusade\PhpLom\Nodes\Getter;
use Crusade\PhpLom\Nodes\Setter;
use Crusade\PhpLom\ValueObject\GeneratedMethodData;
use Crusade\PhpLom\ValueObject\PhpDoc;

class DocFactory
{
    public function buildForGeneratedMethod(GeneratedMethodData $generatedMethodData): PhpDoc
    {
        if ($generatedMethodData->getPropertyData()->getAnnotation() instanceof Getter) {
            return $this->getter($generatedMethodData);
        }

        if ($generatedMethodData->getPropertyData()->getAnnotation() instanceof Setter) {
            return $this->setter($generatedMethodData);
        }

        throw new \LogicException('Unsupported Annotation');
    }

    private function getter(GeneratedMethodData $generatedMethodData): PhpDoc
    {
        $type = $generatedMethodData->getPropertyData()->getPropertyType();
        $name = $generatedMethodData->getFunctionName();

        return new PhpDoc(" * @method $type $name()\n  ");
    }

    private function setter(GeneratedMethodData $generatedMethodData): PhpDoc
    {
        $type = $generatedMethodData->getPropertyData()->getPropertyType();
        $propertyName = $generatedMethodData->getPropertyData()->getPropertyName();
        $name = $generatedMethodData->getFunctionName();


        return new PhpDoc("* @method void $name($type \$$propertyName)\n  ");
    }
}