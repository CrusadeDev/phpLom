<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast\AstModifier;

use Crusade\PhpLom\ValueObject\PhpDoc;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassDocReplacementVisitor extends NodeVisitorAbstract
{
    private PhpDoc $phpDoc;

    public function __construct(PhpDoc $phpDoc)
    {
        $this->phpDoc = $phpDoc;
    }

    public function enterNode(Node $node): ?Node\Stmt\Class_
    {
        if ($node instanceof Node\Stmt\Class_ === false) {
            return null;
        }

        $node->setDocComment($this->phpDoc->toDoc());
    }
}