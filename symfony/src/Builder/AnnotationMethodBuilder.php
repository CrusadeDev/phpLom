<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Builder;

use Crusade\PhpLom\Factory\GetterFactory;
use Crusade\PhpLom\Factory\SetterFactory;
use Crusade\PhpLom\Nodes\PropertyData;
use Crusade\PhpLom\Nodes\Getter;
use Crusade\PhpLom\Nodes\Setter;
use PhpParser\Node\Stmt;

class AnnotationMethodBuilder
{
    private GetterFactory $getterFactory;
    private SetterFactory $setterFactory;

    public function __construct()
    {
        $this->getterFactory = new GetterFactory();
        $this->setterFactory = new SetterFactory();
    }

    public function buildForAnnotation(PropertyData $propertyData): Stmt\ClassMethod
    {
        if ($propertyData->getAnnotation() instanceof Getter) {
            return $this->getterFactory->build($propertyData);
        }

        if ($propertyData->getAnnotation() instanceof Setter) {
            return $this->setterFactory->build($propertyData);
        }

        throw new \LogicException('Unsupported Annotation');
    }
}