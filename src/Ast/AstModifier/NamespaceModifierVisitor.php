<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\AstModifier;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class NamespaceModifierVisitor extends NodeVisitorAbstract
{
    private Node\Stmt\Namespace_ $namespace;

    public function __construct(Node\Stmt\Namespace_ $namespace)
    {
        $this->namespace = $namespace;
    }

    public function enterNode(Node $node): ?Node\Stmt\Namespace_
    {
        if ($node instanceof Node\Stmt\Namespace_ === false) {
            return null;
        }

        return $this->namespace;
    }
}