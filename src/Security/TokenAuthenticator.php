<?php

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    public const HEADER_NAME = 'X-AUTH-TOKEN';
    private $em;
    private $repository;
    private $translator;

    public function __construct(EntityManagerInterface $em, UserRepository $repository, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    public function supports(Request $request)
    {
        return $request->headers->has(self::HEADER_NAME);
    }

    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get(self::HEADER_NAME),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiToken = $credentials['token'];

        if (null === $apiToken) {
            return;
        }

        return $this->repository->findOneBy(compact('apiToken'));
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'error' => $this->translator->trans('error.invalid_credentials')
        ], Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => $this->translator->trans('error.auth_needed')
        ], Response::HTTP_FORBIDDEN);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
