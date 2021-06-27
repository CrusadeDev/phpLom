<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\AstFinder;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassFinderVisitor extends NodeVisitorAbstract
{
    private ?Node\Stmt\Class_ $class = null;

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_ === false) {
            return null;
        }

        $this->class = $node;

        return parent::enterNode($node);
    }

    public function hasClass(): bool
    {
        return $this->class !== null;
    }

    public function getClass(): Node\Stmt\Class_
    {
        if ($this->hasClass() === true) {
            throw new \LogicException('Class was not found in file');
        }

        return $this->class;
    }
}