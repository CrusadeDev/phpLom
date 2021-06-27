<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\AstFinder;

use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class AstFinder
{
    /**
     * @param Stmt[] $ast
     */
    public function findClass(array $ast): ?Class_
    {
        $classFinder = new ClassFinderVisitor();

        $this->traverse($classFinder, $ast);

        if ($classFinder->hasClass() === false) {
            return null;
        }

        return $classFinder->getClass();
    }

    /**
     * @param Stmt[] $ast
     */
    public function findNamespace(array $ast): ?Stmt\Namespace_
    {
        $namespaceFinder = new NamespaceFinderVisitor();

        $this->traverse($namespaceFinder, $ast);

        if ($namespaceFinder->hasNamespace() === false) {
            return null;
        }

        return $namespaceFinder->getNamespace();
    }

    /**
     * @param Stmt[] $ast
     */
    private function traverse(NodeVisitorAbstract $visitor, array $ast): void
    {
        $traverser = new NodeTraverser();

        $traverser->addVisitor($visitor);

        $traverser->traverse($ast);
    }
}