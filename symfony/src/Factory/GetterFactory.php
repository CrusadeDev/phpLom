<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Factory;

use Crusade\PhpLom\Nodes\PropertyData;
use PhpParser\Builder\Method;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;

class GetterFactory
{
    public function build(PropertyData $PropertyData): ClassMethod
    {
        $builder = new Method($this->buildName($PropertyData->getPropertyName()));
        $builder->makePublic();
        if ($PropertyData->getPropertyType() !== '') {
            $builder->setReturnType($PropertyData->getPropertyType());
        }

        $v = new Variable('this');
        $f = new PropertyFetch($v, $PropertyData->getPropertyName());
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