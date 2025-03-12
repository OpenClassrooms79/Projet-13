<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

use function sprintf;

// https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/2-data-customization.html
class ApiListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess',
            JWTAuthenticatedEvent::class => 'onJWTAuthenticated',
        ];
    }

    /**
     * méthode appelée après une authentification username/password réussie
     *
     * @param AuthenticationSuccessEvent $event
     * @return void
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        /**
         * @var User $user
         */
        $user = $event->getUser();
        $this->checkApiEnabled($user);
    }

    /**
     * méthode appelée lorsqu'un token JWT valide est fourni, avant de renvoyer la réponse JSON attendue
     *
     * @param JWTAuthenticatedEvent $event
     * @return void
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $event): void
    {
        /** @var User|null $user */
        $user = $event->getToken()->getUser();

        if (!$user instanceof User) {
            throw new CustomUserMessageAuthenticationException('Utilisateur non trouvé.');
        }

        $this->checkApiEnabled($user);
    }

    private function checkApiEnabled(User $user): void
    {
        if (!$user->isApiEnabled()) {
            throw new AccessDeniedHttpException(sprintf("Accès API non activé pour l'utilisateur %s", $user->getUserIdentifier()));
        }
    }
}