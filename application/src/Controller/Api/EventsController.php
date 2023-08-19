<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * I didn't extend this controller from AbstractApiController because in this app it will be only get calls
 * and I don't want to install additional package.
 */
class EventsController extends AbstractController
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/api/events', name: 'api_events')]
    public function index(): JsonResponse
    {
        $events = $this->eventService->getEvents();
        $this->serializer->serialize($events, 'json');

        return $this->json($events, Response::HTTP_OK);
    }
}
