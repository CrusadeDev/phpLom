<?php

declare(strict_types=1);

namespace Crusade\PhpLom\Tests\PhpParserService;

use Crusade\PhpLom\Ast\ValueObject\PrintedClass;
use Crusade\PhpLom\Decorator\Annotation\Getter;
use Crusade\PhpLom\Decorator\ValueObject\PropertyData;
use Crusade\PhpLom\Factory\GetterFactory;
use Crusade\PhpLom\ValueObject\Path;
use Illuminate\Support\Collection;

class PhpParserServiceTestData
{
    public function getClassToTest(): Path
    {
        return new Path(__DIR__.'/../TestData/Annotations/ClassWithGetterAnnotationOnClass.php');
    }

    public function getClassWithoutNamespace(): Path
    {
        return new Path(__DIR__.'/../TestData/ClassWithoutNamespace.php');
    }

    public function getFileWithoutClassWithNamespace(): Path
    {
        return new Path(__DIR__.'/../TestData/FileWIthNamespaceWithoutClass.php');
    }

    public function getGeneratedMethod(): Collection
    {
        $getterBuilder = new GetterFactory();

        $propertyData = new PropertyData(new Getter(), 'test', 'string');

        return new Collection([$getterBuilder->build($propertyData)]);
    }

    public function getPrintedClass(): PrintedClass
    {
        return new PrintedClass(
            '<?php

declare (strict_types=1);
namespace Crusade\PhpLom\Tests\TestData\Annotations;

use Crusade\PhpLom\Decorator\Annotation\Getter;
/**
 * @Getter
 */
class ClassWithGetterAnnotationOnClass
{
    private string $property;
}'
        );
    }
}