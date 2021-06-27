<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Annotation;

use Crusade\PhpLom\Decorator\Interfaces\DecoratorDataInterface;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
use Illuminate\Support\Collection;

class AnnotationService
{
    private PropertyReader $propertyReader;
    private ClassReader $classReader;

    public function __construct()
    {
        $this->propertyReader = new PropertyReader();
        $this->classReader = new ClassReader();
    }

    /**
     * @param string $namespace
     * @return Collection<DecoratorDataInterface>
     * @throws \ReflectionException
     */
    public function readAnnotations(string $namespace): Collection
    {
        $reflection = new \ReflectionClass($namespace);

        $annotationsFromClass = $this->classReader->read($reflection);
        $annotationsFromProperty = $this->propertyReader->read(new Collection($reflection->getProperties()));

        $this->mergeUnique($annotationsFromClass, $annotationsFromProperty);

        return $annotationsFromClass;
    }

    /** TODO extract to helper */
    private function mergeUnique(Collection $annotationsFromClass, Collection $annotationsFromProperty): void
    {
        $annotationsFromProperty->each(
            function (PropertyData $data) use ($annotationsFromClass) {
                if ($annotationsFromClass->contains($data) === true) {
                    return;
                }

                $annotationsFromClass->add($data);
            }
        );
    }
}