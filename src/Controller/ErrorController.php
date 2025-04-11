<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use function sprintf;

class ErrorController extends AbstractController
{
    /**
     * Erreur de produit non trouvé
     *
     * @param string $title
     * @param string $message
     * @param int $id
     * @return Response
     */
    public function productNotFound(string $title, string $message, int $id): Response
    {
        return $this->render(
            'error/product.html.twig',
            [
                'title' => $title,
                'message' => sprintf($message, $id),
            ],
            new Response('', Response::HTTP_NOT_FOUND),
        );
    }

    /**
     * Erreurs HTTP
     *
     * @param int $code
     * @param string $message
     * @return Response
     */
    public function HttpError(int $code, string $message): Response
    {
        return $this->render('error/http.html.twig', [
            'code' => $code,
            'message' => $message,
        ]);
    }

    /**
     * Erreur interne du serveur
     *
     * @return Response
     */
    public function InternalServerError(): Response
    {
        return $this->render('error/server.html.twig');
    }
}
