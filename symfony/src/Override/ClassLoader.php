<?php

declare(strict_types=1);

namespace App\Override;

use App\Command\Generate;
use App\Parser\DocParser;
use App\Parser\FileParser;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Finder\SplFileInfo;

class ClassLoader
{
    /**
     * @throws \JsonException
     */
    public static function load(): void
    {
        $filePath = "/var/www/symfony/var/Override/result.json";

        if (file_exists($filePath)) {
            $config = json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);
        } else {
            $config = [];
        }


        spl_autoload_register(
            function (string $className) use ($config) {
                if (array_key_exists($className, $config) === false) {
                    return;
                }

                include $config[$className];
            },
            true,
            true
        );

        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public static function parse(SplFileInfo $filePath): void
    {
        $fileParser = new FileParser();
        $cache = new OverrideConfig();

        $namespace = $fileParser->getNamespace($filePath->getPathname());

        if ($namespace === null) {
            return;
        }

        $annotations = (new DocParser())->parse($namespace);

        if ($annotations->isEmpty()) {
            return;
        }

        $resultFile = $fileParser->parse($filePath, $annotations);

        $cache->appendToCache($namespace, $resultFile);
    }
}