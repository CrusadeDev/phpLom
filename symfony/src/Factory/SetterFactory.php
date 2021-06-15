<?php

declare(strict_types=1);

namespace App\Factory;

use App\Nodes\Annotation;
use PhpParser\Builder\Method;
use PhpParser\Builder\Param;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;

class SetterFactory
{
    public function build(Annotation $annotation): ClassMethod
    {
        $builder = new Method($this->buildName($annotation->getPropertyName()));
        $builder->makePublic();
        $builder->setReturnType('void');
        $param = new Param($annotation->getPropertyName());

        if ($annotation->getPropertyType() !== '') {
            $param->setType($annotation->getPropertyType());
        }
        $param = $param->getNode();
        $builder->addParam($param);

        $variable = new Variable($annotation->getPropertyName());

        $v = new Variable('this');
        $f = new PropertyFetch($v, $annotation->getPropertyName());
        $return = new Assign($f,$variable);
        $builder->addStmt($return);

        return $builder->getNode();
    }

    private function buildName(string $propertyName): string
    {
        $name = ucfirst($propertyName);

        return "set$name";
    }
}