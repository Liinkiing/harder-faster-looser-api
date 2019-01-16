<?php

namespace App\Controller\Api;

use App\Controller\ApiController;
use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/leaderboards")
 */
class LeaderboardController extends ApiController
{

    /**
     * @Route("/all", name="api.leaderboard.all", methods={"GET"})
     */
    public function all(RequestStack $request, PlayerRepository $repository): JsonResponse
    {
        $first = $request->getCurrentRequest()->query->get('first', 10);

        return $this->json(
            $repository->findFirst($first)
        );
    }

    /**
     * @Route("/rank/{score}", requirements={"score"="\d+"}, name="api.leaderboard.rank", methods={"GET"})
     */
    public function rank(int $score, PlayerRepository $repository): JsonResponse
    {
        return $this->json(
            $repository->findRankForScore($score)
        );
    }

}
