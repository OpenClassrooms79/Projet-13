<?php

namespace App\Controller;

use App\Form\AddToCartType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class MainController extends AbstractController
{
    public const ERROR_TITLE = 'Produit inexistant';
    public const ERROR_SHOW = "Impossible d'afficher le produit n°%d car il n'existe pas.";

    public function __construct(private readonly ProductRepository $productRepository) {}

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'products' => $this->productRepository->findAll(),
        ]);
    }

    #[Route('/produit/{id}', name: 'product_show', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function show(int $id, Request $request, CartService $cartService): Response
    {
        $product = $this->productRepository->find($id);
        if ($product === null) {
            return $this->forward('App\Controller\ErrorController::index', [
                'title' => self::ERROR_TITLE,
                'message' => self::ERROR_SHOW,
                'id' => $id,
            ]);
        }

        $form = $this->createForm(AddToCartType::class);

        // suppression du compte
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cartService->add($id);
            return $this->redirect($request->getUri()); // rediriger vers la route actuelle
        }

        return $this->render('main/show.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
}
