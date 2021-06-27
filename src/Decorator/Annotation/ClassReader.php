<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Annotation;

use Crusade\PhpLom\Decorator\Interfaces\DecoratorDataInterface;
use Crusade\PhpLom\Decorator\ValueObject\ClassData;
use Crusade\PhpLom\Decorator\Factory\PropertyDataFactory;
use Crusade\PhpLom\Decorator\Interfaces\AnnotationInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Support\Collection;

class ClassReader
{
    private AnnotationReader $reader;
    private PropertyDataFactory $factory;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
        $this->factory = new PropertyDataFactory();
    }

    /**
     * @param \ReflectionClass $class
     * @return Collection<DecoratorDataInterface>
     */
    public function read(\ReflectionClass $class): Collection
    {
        $annotations = AnnotationsValueObject::getClassAnnotations()
            ->transform(
                fn(string $classAnnotationsName): ?AnnotationInterface => $this->reader->getClassAnnotation(
                    $class,
                    $classAnnotationsName
                )
            )
            ->filter(fn(?AnnotationInterface $annotation) => $annotation !== null);

        if ($annotations->isEmpty()) {
            return new Collection();
        }

        $properties = new Collection($class->getProperties());

        return $annotations
            ->transform(fn(AnnotationInterface $annotation) => $this->handleProperty($annotation, $properties))
            ->flatten();
    }

    /**
     * @param Collection<\ReflectionProperty> $properties
     * @return Collection<DecoratorDataInterface>
     */
    private function handleProperty(AnnotationInterface $annotation, Collection $properties): Collection
    {
        if ($annotation->hasClassAlias() === true) {
            return $properties->map(
                fn(\ReflectionProperty $property) => $this->factory->buildFromPropertyReflection(
                    $property,
                    $annotation
                )
            );
        }

        return new Collection([new ClassData($annotation)]);
    }
}