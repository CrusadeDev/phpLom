<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Decorator;

use Crusade\PhpLom\Decorator\Annotation\AnnotationService;
use Illuminate\Support\Collection;

class DecoratorFacade
{
    private AnnotationService $annotationService;

    public function __construct()
    {
        $this->annotationService = new AnnotationService();
    }

    /**
     * @throws \ReflectionException
     */
    public function readAnnotations(string $classNamespace): Collection
    {
        return $this->annotationService->readAnnotations($classNamespace);
    }
}