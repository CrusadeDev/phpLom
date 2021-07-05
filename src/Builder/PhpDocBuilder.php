<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Builder;

use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
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
        $this->start();
        $this->addAlreadyExistingDoc($class);

        $methods
            ->transform(fn(PropertyData $data) => $this->docFactory->buildForGeneratedMethod($data))
            ->each(fn(PhpDoc $doc) => $this->addDoc((string)$doc));

        $this->end();

        $doc = new PhpDoc($this->doc);

        $this->doc = '';

        return $doc;
    }

    private function start(): void
    {
        $this->doc = "/** \n";
    }

    private function addDoc(string $doc): void
    {
        $this->doc .= $doc;
    }

    private function end(): void
    {
        $this->doc .= '*/';
    }

    private function addAlreadyExistingDoc(Class_ $class): void
    {
        (string)$currentDoc = $class->getDocComment();

        $phpdocElements = explode("\n", $currentDoc->getText());

        if (count($phpdocElements) >= 3) {
            array_pop($phpdocElements);
            foreach ($phpdocElements as $element) {
                $this->addDoc($element);
            }
        }
    }
}