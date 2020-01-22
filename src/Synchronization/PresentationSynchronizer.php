<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Synchronization;


use App\ApiClient\ApiClientV1;
use App\ApiClient\RenderClient;
use App\Cache\CacheInterface;
use App\Repository\ScheduleRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;

class PresentationSynchronizer implements PresentationSynchronizerInterface
{
    public const CACHE_FORMAT = 'presentation_%d';

    /** @var ApiClientV1 */
    private $apiClientV1;

    /** @var CacheInterface */
    private $cache;

    /** @var ScheduleRepositoryInterface */
    private $scheduleRepository;

    /** @var RenderClient */
    private $renderClient;

    /** @var RouterInterface */
    private $router;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ApiClientV1 $apiClientV1,
        CacheInterface $cache,
        ScheduleRepositoryInterface $scheduleRepository,
        RenderClient $renderClient,
        RouterInterface $router,
        LoggerInterface $logger
    ) {
        $this->apiClientV1 = $apiClientV1;
        $this->cache = $cache;
        $this->scheduleRepository = $scheduleRepository;
        $this->renderClient = $renderClient;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function synchronize(): void
    {
        $scheduledPresentations = $this->scheduleRepository->getScheduledPresentations();

        foreach ($scheduledPresentations as $scheduledPresentation) {
            $cacheFileName = \sprintf(self::CACHE_FORMAT, $scheduledPresentation->getPresentationId());
            $ifModifiedSince = new \DateTime('@0');
            if ($this->cache->has($cacheFileName)) {
                $presentation = $this->cache->getUnserialized($cacheFileName);
                $ifModifiedSince = $presentation->getLastModified();
            }

            $presentation = $this->apiClientV1->getPresentation($scheduledPresentation->getPresentationId(), $ifModifiedSince);
            if (null === $presentation) {
                // Not modified on server, do nothing.
                continue;
            }

            // First save presentation
            $this->cache->setSerialized($cacheFileName, $presentation);

            try {
                // Now get rendered presentation
                $presentationContent = $this->renderClient->getRenderedPresentation($presentation->getId());

                $presentationContent = preg_replace_callback_array(
                    [
                        '/(?P<tag>data-background-image)=(?P<quotes>[\'"])(?P<url>.+)\2/U' => [$this, 'pregReplaceUrlCallback'],
                        '/(?P<tag>src)=(?P<quotes>[\'"])(?P<url>.+)\2/U' => [$this, 'pregReplaceUrlCallback'],
                        '/(?P<tag><link.*href)=(?P<quotes>[\'"])(?P<url>.+)\2(?P<after>.*>)/U' => [$this, 'pregReplaceUrlCallback'],
                    ],
                    $presentationContent
                );

                // Save again now with contents
                $presentation->setRenderedPresentation($presentationContent);
                $this->cache->setSerialized($cacheFileName, $presentation);
            } catch (\Throwable $exception) {
                // Could not download presentation.
                $this->logger->error(
                    'Could not download presentation with id ' . $scheduledPresentation->getPresentationId() .
                    '. Instance of ' . \get_class($exception) . ' thrown, message: ' . $exception->getMessage(),
                    [ 'exception' => $exception ]
                );
            }
        }
    }

    private function pregReplaceUrlCallback(array $matches): string
    {
        // Download $matches['url'] and replace by local cache url
        $url = $matches['url'];

        $cacheKey = $this->cache->getHashedKey($url);
        $fullCacheFilePath = $this->cache->getFullFilePath($url);

        $this->renderClient->downloadFile($url, $fullCacheFilePath);

        $quotes = $matches['quotes'];
        $cachedUrl = $this->router->generate('cache-get-file', ['key' => $cacheKey]);
        $after = (isset($matches['after'])) ? $matches['after'] : '';

        return $matches['tag'] . '=' . $quotes . $cachedUrl . $quotes . $after;
    }
}
