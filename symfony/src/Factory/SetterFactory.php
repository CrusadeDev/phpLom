<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Factory;

use Crusade\PhpLom\Nodes\PropertyData;
use PhpParser\Builder\Method;
use PhpParser\Builder\Param;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;

class SetterFactory
{
    public function build(PropertyData $PropertyData): ClassMethod
    {
        $builder = new Method($this->buildName($PropertyData->getPropertyName()));
        $builder->makePublic();
        $builder->setReturnType('void');
        $param = new Param($PropertyData->getPropertyName());

        if ($PropertyData->getPropertyType() !== '') {
            $param->setType($PropertyData->getPropertyType());
        }
        $param = $param->getNode();
        $builder->addParam($param);

        $variable = new Variable($PropertyData->getPropertyName());

        $v = new Variable('this');
        $f = new PropertyFetch($v, $PropertyData->getPropertyName());
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