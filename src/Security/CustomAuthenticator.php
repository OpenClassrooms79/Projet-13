<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class CustomAuthenticator extends JWTAuthenticator
{
    public function authenticate(Request $request): Passport
    {
        //$passport = parent::authenticate($request);
        throw new AccessDeniedException('4) Accès API non activé');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        //dump('CustomAuthenticator::onAuthenticationSuccess est appelé');

        //return new JsonResponse('Accès API non activé', Response::HTTP_FORBIDDEN);
        //$response = parent::onAuthenticationSuccess($request, $token, $firewallName);

        // Appeler la méthode parente pour obtenir la réponse
        //$response = parent::onAuthenticationSuccess($request, $token, $firewallName);
        return new JsonResponse('5) Accès API non activé', Response::HTTP_FORBIDDEN);
        //throw new AccessDeniedException('1) Accès API non activé');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        //$response = parent::onAuthenticationFailure($request, $exception);
        //$exception = new AuthenticationException('2) Accès API non activé');
        throw new AccessDeniedException('3) Accès API non activé');
    }
}