<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\AstModifier;

use Crusade\PhpLom\Ast\AstFinder\AstFinder;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class AstModifier
{
    private AstFinder $astFinder;

    public function __construct()
    {
        $this->astFinder = new AstFinder();
    }

    /**
     * @param Stmt[] $ast
     * @return Stmt[]
     */
    public function replaceClass(array $ast, Class_ $class): array
    {
        $namespace = clone $this->astFinder->findNamespace($ast);
        $modifiedAst = $this->replaceNamespace($ast, $namespace);

        $classFinder = new ClassModifierVisitor($class);

        return $this->traverse($classFinder, $modifiedAst);
    }

    /**
     * @param Stmt[] $ast
     * @return Stmt[]
     */
    public function replaceNamespace(array $ast, Stmt\Namespace_ $namespace): array
    {
        $namespaceModifierVisitor = new NamespaceModifierVisitor($namespace);

        return $this->traverse($namespaceModifierVisitor, $ast);
    }

    /**
     * @param Stmt[] $ast
     * @return Stmt[]
     */
    private function traverse(NodeVisitorAbstract $visitor, array $ast): array
    {
        $traverser = new NodeTraverser();

        $traverser->addVisitor($visitor);

        return $traverser->traverse($ast);
    }
}