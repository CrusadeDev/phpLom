<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Builder;

use Crusade\PhpLom\Factory\DocFactory;
use Crusade\PhpLom\ValueObject\GeneratedMethodData;
use Crusade\PhpLom\ValueObject\PhpDoc;
use Illuminate\Support\Collection;

class PhpDocBuilder
{
    private string $doc;
    private DocFactory $docFactory;

    public function __construct()
    {
        $this->doc = '';
        $this->docFactory = new DocFactory();
    }

    /**
     * @param Collection<GeneratedMethodData> $methods
     * @return PhpDoc
     */
    public function buildForGeneratedMethods(Collection $methods): PhpDoc
    {
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