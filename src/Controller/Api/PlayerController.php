<?php

namespace App\Controller\Api;


use App\Controller\ApiController;
use App\Entity\Player;
use App\Form\PlayerFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/player")
 */
class PlayerController extends ApiController
{

    /**
     * @Route("/new", name="api.player.new", methods={"PUT", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormFactoryInterface $factory): JsonResponse
    {
        $player = new Player();
        $data = json_decode($request->getContent(), true);

        $form = $factory->create(PlayerFormType::class, $player);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($player);
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
