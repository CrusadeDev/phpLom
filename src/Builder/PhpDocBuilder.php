<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Builder;

use Crusade\PhpLom\Factory\PhpDocFactory;
use Crusade\PhpLom\ValueObject\GeneratedMethodData;
use Crusade\PhpLom\ValueObject\PhpDoc;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt\Class_;

class PhpDocBuilder
{
    private string $doc;
    private PhpDocFactory $docFactory;

    public function __construct()
    {
        $this->doc = '';
        $this->docFactory = new PhpDocFactory();
    }

    /**
     * @param Collection<GeneratedMethodData> $methods
     * @param Class_ $class
     * @return PhpDoc
     */
    public function buildForGeneratedMethods(Collection $methods, Class_ $class): PhpDoc
    {
        (string)$currentDoc = $class->getDocComment();

        $this->start();

        $methods
            ->transform(fn(GeneratedMethodData $data) => $this->docFactory->buildForGeneratedMethod($data))
            ->each(fn(PhpDoc $doc) => $this->addDoc($doc));

        $this->end();

        return new PhpDoc($this->doc);
    }

    private function start(): void
    {
        $this->doc = "/** \n";
    }

    private function addDoc(PhpDoc $doc): void
    {
        $this->doc .= $doc;
    }

    private function end(): void
    {
        $this->doc .= '*/';
    }
}