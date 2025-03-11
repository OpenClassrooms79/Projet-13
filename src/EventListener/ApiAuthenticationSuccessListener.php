<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiAuthenticationSuccessListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        // Code à exécuter après une authentification réussie via un token JWT

        /**
         * @var User $user
         */
        $user = $event->getUser();
        if (!$user->isApiEnabled()) {
            throw new AccessDeniedHttpException(sprintf('Accès API non activé pour l\'utilisateur %s', $user->getUserIdentifier()));
        }
    }

    public function onLexikJwtAuthenticationHandlerAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        throw new AccessDeniedException('7) Accès API non activé');
    }
}