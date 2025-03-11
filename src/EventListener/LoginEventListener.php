<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginEventListener implements EventSubscriberInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if ($request->getPathInfo() === '/api/login') {
            $username = $request->get('username');
            $password = $request->get('password');

            // Exécutez du code custom pour vérifier les informations d'authentification
            if (!$thizs->verifyCredentials($username, $password)) {
                $event->setResponse(new Response('Authentification échouée', 401));
                $event->stopPropagation();
            }
        }
    }

    private function verifyCredentials($username, $password)
    {
        // Code custom pour vérifier les informations d'authentification
        // Retourne true si l'authentification est réussie, false sinon
    }
}