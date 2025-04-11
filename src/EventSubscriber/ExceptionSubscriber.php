<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Http\FirewallMapInterface;
use Symfony\Config\Security\FirewallConfig;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly KernelInterface $kernel,
        private readonly FirewallMapInterface $firewallMap,
        private readonly RequestStack $requestStack,
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Stopper si l'exception a déjà été gérée
        if ($request->attributes->get('_exception_handled')) {
            return;
        }

        /**
         * @var FirewallConfig $firewallConfig
         * @see config/packages/security.yaml
         */
        $firewallConfig = $this->firewallMap->getFirewallConfig($request);
        if ($firewallConfig->getName() === 'main') {
            // exception sur le site

            $this->requestStack->getCurrentRequest()->setLocale('fr');
            $subRequest = $request->duplicate([], null);
            if ($exception instanceof HttpException) {
                // erreur HTTP
                $subRequest->attributes->set('_controller', 'App\Controller\ErrorController::HttpError');
                $subRequest->attributes->set('code', $exception->getStatusCode());
                $subRequest->attributes->set('message', $this->translator->trans($exception->getMessage(), [], 'messages'));
            } else {
                // erreur interne du serveur
                $subRequest->attributes->set('_controller', 'App\Controller\ErrorController::InternalServerError');
            }
            $response = $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            $event->setResponse($response);
        } else {
            // exception dans l'API

            if (($exception instanceof HttpException) && array_key_exists($exception->getStatusCode(), Response::$statusTexts)) {
                // erreur HTTP
                $event->setResponse(new JsonResponse([
                    'errors' => [
                        'status' => $exception->getStatusCode(),
                        'title' => $this->translator->trans(Response::$statusTexts[$exception->getStatusCode()], [], 'messages'),
                        'detail' => $this->translator->trans($exception->getMessage(), [], 'messages'),
                    ],
                ], $exception->getStatusCode()));
            } else {
                // erreur interne du serveur
                $event->setResponse(new JsonResponse('Erreur interne du serveur', Response::HTTP_INTERNAL_SERVER_ERROR));
            }
        }
    }


    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
