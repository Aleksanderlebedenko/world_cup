<?php

namespace App\Controller;

use App\Service\ScoreboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScoreboardController extends AbstractController
{
    public function __construct(
        readonly private ScoreboardService $scoreboardService,
    ) {
    }

    #[Route('/', name: 'app_scoreboard')]
    public function index(): Response
    {
        return $this->render('scoreboard/index.html.twig', [
            'upcomingPairs' => $this->scoreboardService->getUpcomingPairs(),
            'currentPairs' => $this->scoreboardService->getCurrentPairs(),
            'finishedPairs' => $this->scoreboardService->getFinishedPairs(),
        ]);
    }
}
