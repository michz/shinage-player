<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Command;

use App\Cache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends Command
{
    /** @var CacheInterface */
    private $cache;

    public function __construct(
        CacheInterface $cache
    ) {
        parent::__construct();
        $this->cache = $cache;
    }

    protected function configure()
    {
        $this
            ->setName('shinage:cache:clear')
            ->setDescription('Clears the local cache completely.')
            ->setHelp(
                'This clears all offline data. Instant no playback is possible any more. ' .
                'You have to synchronize immediately after clearing the cache ' .
                'if you want to be able to play back anything.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cache->clear();
        return 0;
    }
}
