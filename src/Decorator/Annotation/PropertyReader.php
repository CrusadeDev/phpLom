<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Annotation;

use Crusade\PhpLom\Decorator\Factory\PropertyDataFactory;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Support\Collection;

class PropertyReader
{
    private AnnotationReader $reader;
    private PropertyDataFactory $builder;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
        $this->builder = new PropertyDataFactory();
    }

    /**
     * @param Collection<\ReflectionProperty> $properties
     * @return Collection<PropertyData>
     */
    public function read(Collection $properties): Collection
    {
        return $properties
            ->transform(fn(\ReflectionProperty $property): Collection => $this->readAllPropertyAnnotations($property))
            ->flatten();
    }

    /**
     * @return Collection<PropertyData>
     */
    private function readAllPropertyAnnotations(\ReflectionProperty $property): Collection
    {
        return AnnotationsValueObject::getPropertyAnnotations()
            ->transform(
                function (string $annotationClassName) use ($property): ?PropertyData {
                    return $this->readAnnotation($property, $annotationClassName);
                }
            )
            ->filter(fn(?PropertyData $annotation): bool => $annotation !== null);
    }

    private function readAnnotation(\ReflectionProperty $property, string $annotationsClassName): ?PropertyData
    {
        $ann = $this->reader->getPropertyAnnotation($property, $annotationsClassName);

        if ($ann === null) {
            return null;
        }

        return $this->builder->buildFromPropertyReflection($property, $ann);
    }
}
