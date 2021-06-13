<?php

declare(strict_types=1);

namespace App\Parser;

use App\Nodes\Annotation;
use App\Nodes\AnnotationInterface;

use App\Nodes\Getter;
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
     * @return Collection<AnnotationInterface>
     * @throws \ReflectionException
     */
    public function parse(string $classNamespace): Collection
    {
        $classR = new \ReflectionClass($classNamespace);

        return (new Collection($classR->getProperties()))
            ->transform(
                function (ReflectionProperty $property) {
                    $ann = $this->reader->getPropertyAnnotation($property, Getter::class);

                    if ($ann === null) {
                        return null;
                    }

                    $props = $property->getType()->getName();
                    if (strpos($props, '\\') !== false) {
                        $props = '\\'.$props;
                    }

                    return new Annotation($ann, $property->getName(), $props);
                }
            )->filter(fn(?Annotation $annotation) => $annotation !== null);
    }
}