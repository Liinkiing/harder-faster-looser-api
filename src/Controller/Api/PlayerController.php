<?php

namespace App\Controller\Api;


use App\Controller\ApiController;
use App\Entity\Player;
use App\Form\PlayerFormType;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/player")
 */
class PlayerController extends ApiController
{

    /**
     * @Route("/new", name="api.player.new", methods={"PUT", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormFactoryInterface $factory, PlayerRepository $repository): JsonResponse
    {
        $player = new Player();
        $data = json_decode($request->getContent(), true);

        $form = $factory->create(PlayerFormType::class, $player);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($player);
            $player->setRank(
                $repository->findRankForScore($player->getScore())
            );
            $em->flush();

            return $this->json(
                $player,
                Response::HTTP_CREATED
            );
        }

        return $this->json(
            [
                'errors' => $this->createFormErrors($form)
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

}
