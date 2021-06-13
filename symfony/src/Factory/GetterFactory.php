<?php

declare(strict_types=1);

namespace App\Factory;

use App\Nodes\Annotation;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;

class GetterFactory
{
    public function build(Annotation $annotation): ClassMethod
    {
        $builder = new Method($this->buildName($annotation->getPropertyName()));
        $builder->makePublic();
        if ($annotation->getPropertyType() !== '') {
            $builder->setReturnType($annotation->getPropertyType());
        }

        $v = new Variable('this');
        $f = new PropertyFetch($v, $annotation->getPropertyName());
        $return = new Return_($f);
        $builder->addStmt($return);

        return $builder->getNode();
    }

    private function buildName(string $propertyName): string
    {
        $name = ucfirst($propertyName);
        return "get$name";
    }
}