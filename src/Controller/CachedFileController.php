<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Controller;

use App\Cache\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CachedFileController extends AbstractController
{
    /** @var CacheInterface */
    private $cache;

    public function __construct(
        CacheInterface $cache
    ) {
        $this->cache = $cache;
    }

    /**
     * @Route(name="cache-get-file", path="/cache/get/{key}", requirements={"key": "[a-zA-Z0-9]+"})
     */
    public function getAction(string $key): Response
    {
        if (false === $this->cache->has($key, true)) {
            throw new NotFoundHttpException();
        }

        return new StreamedResponse(function () use ($key) {
            $this->cache->getStreamed($key, true);
        });
    }
}
