<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Repository;

use App\Cache\CacheInterface;
use App\Model\ScheduledPresentation;
use App\Synchronization\ScheduleSynchronizer;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    /** @var CacheInterface */
    private $cache;

    public function __construct(
        CacheInterface $cache
    ) {
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getScheduledPresentations(): array
    {
        try {
            return $this->cache->getUnserialized(ScheduleSynchronizer::CACHE_FILE);
        } catch (\Throwable $exception) {
            return [];
        }
    }

    public function getScheduledPresentationAt(\DateTime $dateTime): ?ScheduledPresentation
    {
        $schedule = $this->getScheduledPresentations();
        foreach ($schedule as $scheduledPresentation) {
            if ($dateTime >= $scheduledPresentation->getStart() && $dateTime <= $scheduledPresentation->getEnd()) {
                return $scheduledPresentation;
            }
        }

        return null;
    }
}
