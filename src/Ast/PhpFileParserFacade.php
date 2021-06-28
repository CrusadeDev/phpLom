<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast;

use Crusade\PhpLom\Ast\Exceptions\FileDoesNotHaveClassException;
use Crusade\PhpLom\Ast\ValueObject\FileNamespace;
use Crusade\PhpLom\Ast\ValueObject\ParsedFile;
use Crusade\PhpLom\Ast\ValueObject\PrintedClass;
use Crusade\PhpLom\ValueObject\Path;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt\Function_;

class PhpFileParserFacade
{
    private PhpParserService $service;

    public function __construct()
    {
        $this->service = new PhpParserService();
    }

    public function parseFileToAst(Path $filePath): ParsedFile
    {
        return $this->service->parseFileToAst($filePath);
    }

    /**
     * @param Collection<Function_> $generatedMethods
     * @param ParsedFile $parsedFile
     * @return ParsedFile
     * @throws FileDoesNotHaveClassException
     */
    public function attachedGeneratedMethodsToClass(Collection $generatedMethods, ParsedFile $parsedFile): ParsedFile
    {
        return $this->service->attachedGeneratedMethodsToClass($generatedMethods, $parsedFile);
    }

    public function printClass(ParsedFile $parsedFile): PrintedClass
    {
        return $this->service->printClass($parsedFile);
    }

    public function getNamespace(ParsedFile $parsedFile): FileNamespace
    {
        return $this->service->getNamespace($parsedFile);
    }

    public function hasClass(ParsedFile $parsedFile): bool
    {
        return $this->service->hasClass($parsedFile);
    }
}