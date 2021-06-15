<?php

declare (strict_types=1);

namespace App\Command;

use App\Nodes\Getter;
use App\Nodes\Setter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method string getValue()
 * @method void setValue(string $value)
 */
class Generate extends Command
{
    /**
     * @inheritDoc
     */
    protected static $defaultName = 'app:generate';
    /**
     * @Getter
     * @Setter
     */
    private string $value = 'value';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(['User Creator 123', '============', '']);
        // outputs a message followed by a "\n"
        $output->writeln('Whoa!');
        // outputs a message without adding a "\n" at the end of the line
        $output->write('You are about to ');
        $output->write('create a user.');

        return Command::SUCCESS;
    }
}