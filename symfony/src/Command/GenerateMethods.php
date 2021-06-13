<?php

declare(strict_types=1);

namespace App\Command;

use App\Override\ClassLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class GenerateMethods extends Command
{
    /**
     * @inheritDoc
     */
    protected static $defaultName = 'app:generate:methods';

    /**
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = '/var/www/symfony/src';

        $finder = new Finder();

        $files = $finder->files()->in($path)->depth('>0');


        foreach ($files as $file) {
            ClassLoader::parse($file);
        }

        return Command::SUCCESS;
    }
}