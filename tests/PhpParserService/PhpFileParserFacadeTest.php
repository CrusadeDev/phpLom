<?php

/** @noinspection ClassConstantCanBeUsedInspection */

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\PhpParserService;

use Crusade\PhpLom\Ast\PhpFileParserFacade;
use PHPUnit\Framework\TestCase;

class PhpFileParserFacadeTest extends TestCase
{
    private PhpFileParserFacade $service;
    private PhpParserServiceTestData $testData;

    public function test_parseFileToAst_ShouldReturnNonEmptyAstAndEnteredClassPath(): void
    {
        $path = $this->testData->getClassToTest();
        $result = $this->service->parseFileToAst($path);

        self::assertNotEmpty($result->getAst());
        self::assertEquals($path, $result->getFilePath());
    }

    public function test_attachedGeneratedMethodsToClass_ShouldAttachMethodsToClass(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getClassToTest());

        $result = $this->service->attachedGeneratedMethodsToClass($this->testData->getGeneratedMethod(), $ast);

        self::assertCount(2, $result->getAst()[1]->stmts[1]->stmts);
    }

    public function test_attachedGeneratedMethodsToClass_ShouldNotModifiedAst(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getClassToTest());

        $this->service->attachedGeneratedMethodsToClass($this->testData->getGeneratedMethod(), $ast);

        self::assertEquals($ast, $this->service->parseFileToAst($this->testData->getClassToTest()));
    }

    public function test_printClass_ShouldPrintClass(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getClassToTest());

        $result = $this->service->printClass($ast);

        self::assertEquals($this->testData->getPrintedClass(), $result);
    }

    public function test_getNamespace_ShouldReturnNamespace_WhenClassHasNamespace(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getClassToTest());
        $result = $this->service->getNamespace($ast);

        self::assertTrue($result->hasNamespace());
        self::assertEquals(
            'Crusade\PhpLom\Tests\TestData\Annotations\ClassWithGetterAnnotationOnClass',
            $result->getNamespace()
        );
    }

    public function test_getNamespace_ShouldReturnNamespace_WhenFileDoesNotHasClass(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getFileWithoutClassWithNamespace());
        $result = $this->service->getNamespace($ast);

        self::assertTrue($result->hasNamespace());
        self::assertEquals('Crusade\PhpLom\Tests\TestData', $result->getNamespace());
    }

    public function test_getNamespace_ShouldNotReturnNamespace_WhenClassHasNamespace(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getClassWithoutNamespace());
        $result = $this->service->getNamespace($ast);

        self::assertFalse($result->hasNamespace());
    }

    public function test_hasClass_ShouldReturnTrueWhenFileContainsClass(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getClassToTest());
        $result = $this->service->hasClass($ast);

        self::assertTrue($result);
    }

    public function test_hasClass_ShouldReturnTrueWhenFileContainsClassButDoesNotHaveNamespace(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getClassWithoutNamespace());
        $result = $this->service->hasClass($ast);

        self::assertTrue($result);
    }

    public function test_hasClass_ShouldReturnFalse_WhenFileDoesNotContainsClass(): void
    {
        $ast = $this->service->parseFileToAst($this->testData->getFileWithoutClassWithNamespace());
        $result = $this->service->hasClass($ast);

        self::assertFalse($result);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PhpFileParserFacade();
        $this->testData = new PhpParserServiceTestData();
    }
}