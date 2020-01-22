<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Synchronization;

use App\ApiClient\ApiClientV1;
use App\Cache\CacheInterface;

class ScheduleSynchronizer implements ScheduleSynchronizerInterface
{
    public const CACHE_FILE = 'schedule.php';

    /** @var ApiClientV1 */
    private $apiClient;

    /** @var CacheInterface */
    private $cache;

    public function __construct(
        ApiClientV1 $apiClient,
        CacheInterface $cache
    ) {
        $this->apiClient = $apiClient;
        $this->cache = $cache;
    }

    public function synchronize(\DateTime $from, \DateTime $to): void
    {
        $schedule = $this->apiClient->getSchedule($from, $to);
        $this->cache->setSerialized(self::CACHE_FILE, $schedule);
    }
}
