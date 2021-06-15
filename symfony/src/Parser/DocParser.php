<?php

declare(strict_types=1);

namespace App\Parser;

use App\Nodes\Annotation;
use App\Nodes\AnnotationInterface;

use App\Nodes\Getter;
use App\Nodes\Setter;
use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Support\Collection;
use ReflectionProperty;

class DocParser
{
    private AnnotationReader $reader;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    /**
     * @param string $classNamespace
     * @return Collection<Collection<AnnotationInterface>>
     * @throws \ReflectionException
     */
    public function parse(string $classNamespace): Collection
    {
        $classR = new \ReflectionClass($classNamespace);

        return (new Collection($classR->getProperties()))
            ->transform(
                function (ReflectionProperty $property): ?Collection {
                    $ann = $this->readAnnotation($property)
                        ->filter(fn(?AnnotationInterface $annotation) => $annotation !== null);

                    if ($ann->isEmpty()) {
                        return null;
                    }

                    $props = $property->getType()->getName();
                    if (strpos($props, '\\') !== false) {
                        $props = '\\'.$props;
                    }

                    return $ann->transform(
                        fn(AnnotationInterface $annotation) => new Annotation($annotation, $property->getName(), $props)
                    );
                }
            )
            ->filter(fn(?Collection $annotation) => $annotation !== null);
    }

    private function readAnnotation(ReflectionProperty $property): Collection
    {
        return new Collection(
            [
                $this->reader->getPropertyAnnotation($property, Getter::class),
                $this->reader->getPropertyAnnotation($property, Setter::class),
            ]
        );
    }
}