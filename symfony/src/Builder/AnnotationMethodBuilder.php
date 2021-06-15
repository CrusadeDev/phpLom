<?php

declare(strict_types=1);

namespace App\Builder;

use App\Factory\GetterFactory;
use App\Factory\SetterFactory;
use App\Nodes\Annotation;
use App\Nodes\Getter;
use App\Nodes\Setter;
use PhpParser\Node\Stmt;

class AnnotationMethodBuilder
{
    private GetterFactory $getterFactory;
    private SetterFactory $setterFactory;

    public function __construct()
    {
        $this->getterFactory = new GetterFactory();
        $this->setterFactory = new SetterFactory();
    }

    public function buildForAnnotation(Annotation $annotation): Stmt
    {
        if ($annotation->getAnnotation() instanceof Getter) {
            return $this->getterFactory->build($annotation);
        }

        if ($annotation->getAnnotation() instanceof Setter) {
            return $this->setterFactory->build($annotation);
        }
    }
}