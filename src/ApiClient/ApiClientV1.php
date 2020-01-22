<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\ApiClient;

use App\Model\Presentation;
use App\Model\ScheduledPresentation;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

class ApiClientV1
{
    /** @var string */
    private $remoteBaseUrl;

    /** @var string */
    private $screenGuid;

    /** @var int */
    private $timeout;

    /** @var Client */
    private $client;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        string $remoteBaseUrl,
        string $screenGuid,
        int $timeout,
        SerializerInterface $serializer
    ) {
        $this->remoteBaseUrl = $remoteBaseUrl;
        $this->screenGuid = $screenGuid;
        $this->timeout = $timeout;

        $this->client = new Client([
            'timeout' => $timeout,
            'base_uri' => $this->remoteBaseUrl,
            'headers' => [
                'X-SCREEN-GUID' => $this->screenGuid,
            ],
        ]);
        $this->serializer = $serializer;
    }

    /**
     * @return array|ScheduledPresentation[]
     */
    public function getSchedule(\DateTime $from, \DateTime $to): array
    {
        $response = $this->client->get(
            'schedule',
            [
                'query' => [
                    'from' => $from->format('Y-m-d H:i:s'),
                    'until' => $to->format('Y-m-d H:i:s'),
                ],
            ]
        );

        return $this->serializer->deserialize(
            $response->getBody()->getContents(),
            'array<' . ScheduledPresentation::class . '>',
            'json'
        );
    }

    public function getPresentation(int $id, ?\DateTime $modificationDate = null): ?Presentation
    {
        if (null === $modificationDate) {
            $modificationDate = new \DateTime('@0');
        }

        $response = $this->client->get(
            'presentations/' . $id,
            [
                'headers' => [
                    'If-Modified-Since' => $modificationDate->format(DATE_RFC7231),
                ],
            ]
        );

        if ($response->getStatusCode() === 304) {
            return null;
        }

        return $this->serializer->deserialize(
            $response->getBody()->getContents(),
            Presentation::class,
            'json'
        );
    }
}
