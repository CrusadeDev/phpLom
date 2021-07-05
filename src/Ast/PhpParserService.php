<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Ast;

use Crusade\PhpLom\Ast\AstFinder\AstFinder;
use Crusade\PhpLom\Ast\AstModifier\AstModifier;
use Crusade\PhpLom\Ast\Exceptions\FileDoesNotHaveClassException;
use Crusade\PhpLom\Ast\ValueObject\FileClass;
use Crusade\PhpLom\Ast\ValueObject\FileNamespace;
use Crusade\PhpLom\Ast\ValueObject\ParsedFile;
use Crusade\PhpLom\Ast\ValueObject\PrintedClass;
use Crusade\PhpLom\File\FileReader;
use Crusade\PhpLom\ValueObject\Path;
use Crusade\PhpLom\ValueObject\PhpDoc;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class PhpParserService
{
    private FileReader $fileReader;
    private AstFinder $traverser;
    private Parser $parser;
    private Standard $printer;
    private AstModifier $astModifier;

    public function __construct()
    {
        $this->fileReader = new FileReader();
        $this->traverser = new AstFinder();
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->printer = new Standard();
        $this->astModifier = new AstModifier();
    }

    public function parseFileToAst(Path $filePath): ParsedFile
    {
        $fileContent = $this->fileReader->readFile($filePath);
        $ast = $this->parser->parse((string)$fileContent);

        return new ParsedFile($ast, $filePath);
    }

    /**
     * @param Collection<Function_> $generatedMethods
     * @throws FileDoesNotHaveClassException
     */
    public function attachedGeneratedMethodsToClass(Collection $generatedMethods, ParsedFile $parsedFile): ParsedFile
    {
        $ast = $parsedFile->getAst();

        $class = clone $this->traverser->findClass($ast);

        if ($class === false) {
            throw FileDoesNotHaveClassException::create($parsedFile);
        }

        $generatedMethods->each(fn(ClassMethod $method) => $class->stmts[] = $method);

        $modifiedAst = $this->astModifier->replaceClass($ast, $class);

        return new ParsedFile($modifiedAst, $parsedFile->getFilePath());
    }

    public function printClass(ParsedFile $parsedFile): PrintedClass
    {
        return new PrintedClass($this->printer->prettyPrintFile($parsedFile->getAst()));
    }

    public function getNamespace(ParsedFile $parsedFile): FileNamespace
    {
        $namespace = $this->traverser->findNamespace($parsedFile->getAst());

        if (is_null($namespace) === true) {
            return new FileNamespace(null);
        }

        $namespaceAsString = $namespace->name->toString();
        $class = $this->traverser->findClass($parsedFile->getAst());

        if (is_null($class) === true) {
            return new FileNamespace($namespaceAsString);
        }

        $className = $class->name->toString();

        return new FileNamespace("$namespaceAsString\\$className");
    }

    public function getClass(ParsedFile $parsedFile): FileClass
    {
        $class = $this->traverser->findClass($parsedFile->getAst());

        if (is_null($class) === true) {
            return new FileClass(null);
        }

        return new FileClass($class);
    }

    public function hasClass(ParsedFile $parsedFile): bool
    {
        $ast = $parsedFile->getAst();

        return $this->traverser->findClass($ast) !== null;
    }

    public function attachDocToClass(ParsedFile $parsedFile, PhpDoc $phpDoc): ParsedFile
    {
        if ($this->hasClass($parsedFile) === false) {
            throw new \LogicException('Ast does not have class');
        }

        $modifiedAst = $this->astModifier->replaceDocOnClass($parsedFile->getAst(), $phpDoc);

        return new ParsedFile($modifiedAst, $parsedFile->getFilePath());
    }
}