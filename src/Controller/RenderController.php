<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Controller;

use App\Repository\PresentationRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class RenderController extends AbstractController
{
    /** @var PresentationRepositoryInterface */
    private $presentationRepository;

    public function __construct(
        PresentationRepositoryInterface $presentationRepository
    ) {
        $this->presentationRepository = $presentationRepository;
    }

    /**
     * @Route(name="render", path="/render/{presentationId}", requirements={"presentationId": "\d+"})
     */
    public function renderAction(string $presentationId): Response
    {
        $presentation = $this->presentationRepository->get((int) $presentationId);
        return new StreamedResponse(function () use ($presentation) {
            echo $presentation->getRenderedPresentation();
        });
    }
}
