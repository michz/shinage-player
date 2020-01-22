<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Repository;

use App\Cache\CacheInterface;
use App\Model\Presentation;
use App\Synchronization\PresentationSynchronizer;

class PresentationRepository implements PresentationRepositoryInterface
{
    /** @var CacheInterface */
    private $cache;

    public function __construct(
        CacheInterface $cache
    ) {
        $this->cache = $cache;
    }

    public function get(int $id): Presentation
    {
        $cacheFileName = \sprintf(PresentationSynchronizer::CACHE_FORMAT, $id);
        return $this->cache->getUnserialized($cacheFileName);
    }
}
