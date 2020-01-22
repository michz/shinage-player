<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Command;

use App\Synchronization\PresentationSynchronizerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizePresentationsCommand extends Command
{
    /** @var PresentationSynchronizerInterface */
    private $presentationSynchronizer;

    public function __construct(
        PresentationSynchronizerInterface $presentationSynchronizer
    ) {
        parent::__construct();
        $this->presentationSynchronizer = $presentationSynchronizer;
    }

    protected function configure()
    {
        $this
            ->setName('shinage:synchronize:presentations')
            ->setDescription('Synchronizes all presentations from the offline schedule.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->presentationSynchronizer->synchronize();
        return 0;
    }
}
