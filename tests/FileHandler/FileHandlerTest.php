<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\FileHandler;

use Crusade\PhpLom\Override\FileHandler;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class FileHandlerTest extends TestCase
{
    private FileHandlerTestData $testData;
    private FileHandler $handler;

    public function test_handle(): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->handler->handle(
            new SplFileInfo(
                '/var/www/html/tests/TestData/Annotations/ClassWithGetterAnnotationOnClass.php',
                '/var/www/html/tests/TestData/Annotations/ClassWithGetterAnnotationOnClass.php',
                '/var/www/html/tests/TestData/Annotations/ClassWithGetterAnnotationOnClass.php'
            )
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->testData = new FileHandlerTestData();
        $this->handler = new FileHandler($this->testData->getConfig());
    }
}