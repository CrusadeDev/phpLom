<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\AnnotationService;

use Crusade\PhpLom\Decorator\DecoratorFacade;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithGetterAnnotationOnClass;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithGetterAnnotationOnProperties;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithMixedAnnotationsOnProperty;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithMixedAnnotationsOnClass;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithNonPropertyAnnotation;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithPropertyWithoutType;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithSetterAnnotationOnClass;
use Crusade\PhpLom\Tests\TestData\Annotations\ClassWithTheSameAnnotationOnClassAndProperty;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;

class DecoratorFacadeTest extends TestCase
{
    private DecoratorFacade $service;
    private AnnotationServiceTestData $testData;

    public function test_readDecorators_ShouldParseGetterAnnotationsOnProperties(): void
    {
        $result = $this->service->readAnnotations(ClassWithGetterAnnotationOnProperties::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(1, $result->all());
        self::assertEquals($this->testData->getExpectedGetterPropertyData(), $result->pop());
    }

    public function test_readDecorators_ShouldParseSetterAnnotationsOnProperties(): void
    {
        $result = $this->service->readAnnotations(ClassWithSetterAnnotationOnClass::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(1, $result->all());
        self::assertEquals($this->testData->getExpectedSetterPropertyData(), $result->pop());
    }


    public function test_readDecorators_ShouldParseGetterOnClassAndReturnAsIfTheyWouldBeOnProperty(): void
    {
        $result = $this->service->readAnnotations(ClassWithGetterAnnotationOnClass::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(1, $result->all());
        self::assertEquals($this->testData->getExpectedGetterPropertyData(), $result->pop());
    }

    public function test_readDecorators_ShouldParseSetterOnClassAndReturnAsIfTheyWouldBeOnProperty(): void
    {
        $result = $this->service->readAnnotations(ClassWithSetterAnnotationOnClass::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(1, $result->all());
        self::assertEquals($this->testData->getExpectedSetterPropertyData(), $result->pop());
    }

    public function test_readDecorators_ShouldParseMultipleAnnotationsOnProperty(): void
    {
        $result = $this->service->readAnnotations(ClassWithMixedAnnotationsOnProperty::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(2, $result->all());
        self::assertEquals(
            $this->testData->getExpectedMixedProperties(),
            $result->all()
        );
    }

    public function test_readDecorators_ShouldParseMultipleAnnotationsOnClass(): void
    {
        $result = $this->service->readAnnotations(ClassWithMixedAnnotationsOnClass::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(2, $result->all());
        self::assertEquals(
            [$this->testData->getExpectedGetterPropertyData(), $this->testData->getExpectedSetterPropertyData()],
            $result->all()
        );
    }

    public function test_readDecorators_ShouldReturnOnlyOneIfAnnotationIsDuplicatedOnClassAndProperty(): void
    {
        $result = $this->service->readAnnotations(ClassWithTheSameAnnotationOnClassAndProperty::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(1, $result->all());
        self::assertEquals(
            $this->testData->getExpectedGetterPropertyData(),
            $result->pop()
        );
    }

    public function test_readDecorators_ShouldHandlePropertiesWithoutData(): void
    {
        $result = $this->service->readAnnotations(ClassWithPropertyWithoutType::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(1, $result->all());
        self::assertEquals(
            $this->testData->getExpectedGetterWithoutType(),
            $result->pop()
        );
    }

    public function test_readDecorators_ShouldHandleNonPropertyAnnotation(): void
    {
        $result = $this->service->readAnnotations(ClassWithNonPropertyAnnotation::class);

        self::assertNotEmpty($result->toArray());
        self::assertCount(1, $result->all());
        self::assertEquals(
            $this->testData->getNonPropertyAnnotation(),
            $result->pop()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DecoratorFacade();
        $this->testData = new AnnotationServiceTestData();
        AnnotationRegistry::registerLoader('class_exists');
    }
}