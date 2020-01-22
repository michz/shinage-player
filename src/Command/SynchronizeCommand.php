<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('shinage:synchronize')
            ->addOption(
                'from',
                'f',
                InputOption::VALUE_REQUIRED,
                'Sychronize everything beginning with this DateTime, default: now',
                'now'
            )
            ->addOption(
                'to',
                't',
                InputOption::VALUE_REQUIRED,
                'Sychronize everything ending with this DateTime, default: now + 1week',
                '+1week'
            )
            ->setDescription('Synchronize everything for local offline usage.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new \DateTime($input->getOption('from'));
        $to = new \DateTime($input->getOption('to'));

        $application = $this->getApplication();

        $application->find('shinage:synchronize:schedule')->run(
            new ArrayInput([
                'command' => 'shinage:synchronize:schedule',
                '--from' => $from->format('Y-m-d H:i:s'),
                '--to' => $to->format('Y-m-d H:i:s'),
            ]),
            $output
        );

        $application->find('shinage:synchronize:presentations')->run(
            new ArrayInput([
                'command' => 'shinage:synchronize:presentations',
            ]),
            $output
        );

        return 0;
    }
}
