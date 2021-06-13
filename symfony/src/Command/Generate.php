<?php

declare(strict_types=1);

namespace App\Command;

use App\Nodes\Annotation;
use App\Nodes\Getter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command
{
    /**
     * @inheritDoc
     */
    protected static $defaultName = 'app:generate';
    /**
     * @Getter
     */
    private string $value = 'value';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(
            [
                'User Creator 1234',
                '============',
                '',
            ]
        );

        // outputs a message followed by a "\n"
        $output->writeln('Whoa!');

        // outputs a message without adding a "\n" at the end of the line
        $output->write('You are about to ');
        $output->write('create a user.');

        $ann = new Annotation(new Getter(), 'asd', 'asdf');

        $t = $ann->getAnnotation();

        return Command::SUCCESS;
    }
}