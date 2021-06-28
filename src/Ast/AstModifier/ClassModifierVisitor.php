<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\AstModifier;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassModifierVisitor extends NodeVisitorAbstract
{
    private Node\Stmt\Class_ $replacementClass;

    public function __construct(Node\Stmt\Class_ $replacementClass)
    {
        $this->replacementClass = $replacementClass;
    }

    public function enterNode(Node $node): ?Node\Stmt\Class_
    {
        if ($node instanceof Node\Stmt\Class_ === false) {
            return null;
        }

        return $this->replacementClass;
    }
}