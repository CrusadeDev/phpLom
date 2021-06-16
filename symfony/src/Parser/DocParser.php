<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Parser;

use Crusade\PhpLom\Nodes\PropertyData;
use Crusade\PhpLom\Nodes\AnnotationInterface;

use Crusade\PhpLom\Nodes\Getter;
use Crusade\PhpLom\Nodes\Setter;
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
                        fn(AnnotationInterface $annotation) => new PropertyData($annotation, $property->getName(), $props)
                    );
                }
            )
            ->filter(fn(?Collection $PropertyData) => $PropertyData !== null);
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