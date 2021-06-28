<?php

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
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PhpFileParserFacade();
        $this->testData = new PhpParserServiceTestData();
    }
}