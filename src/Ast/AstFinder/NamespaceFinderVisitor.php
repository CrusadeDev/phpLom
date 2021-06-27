<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\AstFinder;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class NamespaceFinderVisitor extends NodeVisitorAbstract
{
    private ?Node\Stmt\Namespace_ $namespace = null;

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_ === false) {
            return null;
        }

        $this->namespace = $node;

        return parent::enterNode($node);
    }

    public function hasNamespace(): bool
    {
        return $this->namespace !== null;
    }

    public function getNamespace(): Node\Stmt\Namespace_
    {
        if ($this->hasNamespace() === true) {
            throw new \LogicException('Class was not found in file');
        }

        return $this->namespace;
    }
}