<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route(path: '/nos-evenements', name: 'list-events')]
class ListEventsController
{
    public function __invoke(Environment $twig, EventRepository $eventRepository): Response
    {
        return new Response($twig->render('event/index.html.twig', [
            'events' => $eventRepository->findEventsPublished()
        ]), Response::HTTP_OK);
    }
}
