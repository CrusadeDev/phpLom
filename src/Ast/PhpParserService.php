<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast;

use Crusade\PhpLom\Ast\AstFinder\AstFinder;
use Crusade\PhpLom\Ast\Exceptions\FileDoesNotHaveClassException;
use Crusade\PhpLom\Ast\ValueObject\FileNamespace;
use Crusade\PhpLom\Ast\ValueObject\ParsedFile;
use Crusade\PhpLom\Ast\ValueObject\PrintedClass;
use Crusade\PhpLom\File\FileReader;
use Crusade\PhpLom\ValueObject\Path;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class PhpParserService
{
    private FileReader $fileReader;
    private AstFinder $traverser;
    private Parser $parser;
    private Standard $printer;

    public function __construct()
    {
        $this->fileReader = new FileReader();
        $this->traverser = new AstFinder();
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Standard();
    }

    public function parseFileToAst(Path $filePath): ParsedFile
    {
        $fileContent = $this->fileReader->readFile($filePath);
        $ast = $this->parser->parse((string)$fileContent);

        return new ParsedFile($ast, $filePath);
    }

    /**
     * @throws FileDoesNotHaveClassException
     */
    public function attachedGeneratedMethodsToClass(Collection $generatedMethods, ParsedFile $parsedFile): ParsedFile
    {
        $ast = $parsedFile->getAst();

        $class = $this->traverser->findClass($ast);

        if ($class === false) {
            throw FileDoesNotHaveClassException::create($parsedFile);
        }

        $generatedMethods->each(fn(ClassMethod $method) => $class->stmts[] = $method);

        return new ParsedFile($ast, $parsedFile->getFilePath());
    }

    public function printClass(ParsedFile $parsedFile): PrintedClass
    {
        return new PrintedClass($this->printer->prettyPrintFile($parsedFile->getAst()));
    }

    public function getNamespace(ParsedFile $parsedFile): FileNamespace
    {
        $namespace = $this->traverser->findNamespace($parsedFile->getAst());

        return new FileNamespace($namespace);
    }

    public function hasClass(ParsedFile $parsedFile): bool
    {
        $ast = $parsedFile->getAst();

        return $this->traverser->findClass($ast) !== null;
    }
}