<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\ValueObject;

use Crusade\PhpLom\Decorator\Interfaces\AnnotationInterface;
use Crusade\PhpLom\Decorator\Interfaces\DecoratorDataInterface;

class PropertyData implements DecoratorDataInterface
{
    private AnnotationInterface $annotation;
    private string $propertyName;
    private ?string $propertyType;

    public function __construct(AnnotationInterface $annotation, string $propertyName, ?string $propertyType)
    {
        $this->annotation = $annotation;
        $this->propertyName = $propertyName;
        $this->propertyType = $propertyType;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getPropertyType(): ?string
    {
        return $this->propertyType;
    }

    public function getAnnotation(): AnnotationInterface
    {
        return $this->annotation;
    }
}