<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Command;

use App\Synchronization\ScheduleSynchronizerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeScheduleCommand extends Command
{
    /** @var ScheduleSynchronizerInterface */
    private $scheduleSynchronizer;

    public function __construct(
        ScheduleSynchronizerInterface $scheduleSynchronizer
    ) {
        parent::__construct();
        $this->scheduleSynchronizer = $scheduleSynchronizer;
    }

    protected function configure()
    {
        $this
            ->setName('shinage:synchronize:schedule')
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
            ->setDescription('Synchronize the schedule for offline usage.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = new \DateTime($input->getOption('from'));
        $to = new \DateTime($input->getOption('to'));

        $this->scheduleSynchronizer->synchronize($from, $to);
        return 0;
    }
}
