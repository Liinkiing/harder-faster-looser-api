<?php

namespace App\Controller\Api;


use App\Controller\ApiController;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/leaderboard")
 */
class LeaderboardController extends ApiController
{

    /**
     * @Route("/all", name="api.leaderboard.index")
     */
    public function index(RequestStack $request, PlayerRepository $repository): JsonResponse
    {
        $first = $request->getCurrentRequest()->query->get('first', 10);

        return $this->json(
            $repository->findFirst($first)
        );
    }

}
