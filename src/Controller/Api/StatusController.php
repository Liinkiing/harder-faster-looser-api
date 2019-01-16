<?php

namespace App\Controller\Api;


use App\Controller\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/status")
 */
class StatusController extends ApiController
{

    /**
     * Dumb route used to wake up my free Heroku dyno when the game starts
     * @Route("/check", name="api.status.check")
     */
    public function check(): JsonResponse
    {
        return $this->json(
            ['status' => 'online']
        );
    }

}
