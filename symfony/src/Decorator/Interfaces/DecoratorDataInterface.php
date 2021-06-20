<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator\Interfaces;

interface DecoratorDataInterface
{
    public function getAnnotation(): AnnotationInterface;
}