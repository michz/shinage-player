<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Controller;

use App\Repository\PresentationRepositoryInterface;
use App\Repository\ScheduleRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class CurrentController extends AbstractController
{
    /** @var ScheduleRepositoryInterface */
    private $scheduleRepository;

    /** @var RouterInterface */
    private $router;

    /** @var string */
    private $remoteRenderBaseUrl;

    /** @var PresentationRepositoryInterface */
    private $presentationRepository;

    public function __construct(
        ScheduleRepositoryInterface $scheduleRepository,
        PresentationRepositoryInterface $presentationRepository,
        RouterInterface $router,
        string $remoteRenderBaseUrl
    ) {
        $this->scheduleRepository = $scheduleRepository;
        $this->router = $router;
        $this->remoteRenderBaseUrl = $remoteRenderBaseUrl;
        $this->presentationRepository = $presentationRepository;
    }

    /**
     * @Route(name="current", path="/current")
     */
    public function currentAction(): Response
    {
        $currentlyScheduledPresentation = $this->scheduleRepository->getScheduledPresentationAt(new \DateTime());
        if (null === $currentlyScheduledPresentation) {
            return new Response('/splash.html');
        }

        $url = $this->router->generate(
            'render',
            ['presentationId' => $currentlyScheduledPresentation->getPresentationId()]
        );

        // If no presentation cached locally, set url to remote render service
        try {
            $presentation = $this->presentationRepository->get($currentlyScheduledPresentation->getPresentationId());
            if (empty($presentation->getRenderedPresentation())) {
                $url = \rtrim($this->remoteRenderBaseUrl, '/') . '/' .
                    $currentlyScheduledPresentation->getPresentationId();
            }
        } catch (\Throwable $exception) {
            // @TODO Log
            $url = \rtrim($this->remoteRenderBaseUrl, '/') . '/' . $currentlyScheduledPresentation->getPresentationId();
        }

        return new Response(
            $url
        );
    }
}
