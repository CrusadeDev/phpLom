<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Parser;

use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
use Crusade\PhpLom\Decorator\Interfaces\AnnotationInterface;

use Crusade\PhpLom\Decorator\Annotation\Getter;
use Crusade\PhpLom\Decorator\Annotation\Setter;
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
     */
    public function parse(string $classNamespace): Collection
    {
        try {
            $classR = new \ReflectionClass($classNamespace);
        } catch (\ReflectionException $e) {
            return new Collection();
        }

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
                        fn(AnnotationInterface $annotation) => new PropertyData(
                            $annotation,
                            $property->getName(),
                            $props
                        )
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