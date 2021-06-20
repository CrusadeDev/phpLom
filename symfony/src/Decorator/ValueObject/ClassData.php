<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\ValueObject;

use Crusade\PhpLom\Decorator\Interfaces\DecoratorDataInterface;
use Crusade\PhpLom\Decorator\Interfaces\AnnotationInterface;

class ClassData implements DecoratorDataInterface
{
    private AnnotationInterface $annotation;

    public function __construct(AnnotationInterface $annotation)
    {
        $this->annotation = $annotation;
    }

    public function getAnnotation(): AnnotationInterface
    {
        return $this->annotation;
    }
}