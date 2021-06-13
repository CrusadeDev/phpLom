<?php

declare(strict_types=1);

namespace App\Nodes;

class Annotation
{
    /**
     * @Getter
     */
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
}