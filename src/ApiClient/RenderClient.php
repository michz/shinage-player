<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\ApiClient;

use App\Exception\FileNotChangedException;
use App\Model\Presentation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class RenderClient
{
    /** @var string */
    private $remoteBaseUrl;

    /** @var string */
    private $screenGuid;

    /** @var int */
    private $timeout;

    /** @var Client */
    private $client;

    public function __construct(
        string $remoteBaseUrl,
        string $screenGuid,
        int $timeout
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
    }

    public function getRenderedPresentation(int $id): string
    {
        $response = $this->client->get(
            (string) $id
        );

        return $response->getBody()->getContents();
    }

    public function downloadFile(string $url, string $localFilePath): void
    {
        $ctime = 0;
        if (\file_exists($localFilePath)) {
            $ctime = \filectime($localFilePath);
        }

        try {
            $this->client->get(
                $url,
                [
                    'sink' => $localFilePath,
                    'headers' => [
                        'If-Modified-Since' => \gmdate(DATE_RFC7231, $ctime),
                    ],
                    'on_headers' => function (ResponseInterface $response) {
                        if (304 === $response->getStatusCode()) {
                            throw new FileNotChangedException('The presentation has not been changed!');
                        }
                    }
                ]
            );
        } catch (RequestException $exception) {
            if (false === $exception->getPrevious() instanceof FileNotChangedException) {
                throw $exception;
            }

            // Everything is fine, nothing to do.
            // Just make sure not to overwrite the sink file.
            // This is a workaround for: https://github.com/guzzle/guzzle/issues/1793
        }
    }
}
