<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    public const ERROR_TITLE = 'Produit inexistant';
    public const ERROR_SHOW = "Impossible d'afficher le produit n°%d car il n'existe pas.";

    #[Route('/', name: 'app_main')]
    public function index(ProductRepository $productRepository): Response
    {
        //dd($productRepository->findAll());
        return $this->render('main/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/produit/{id}', name: 'product_show', requirements: ['id' => Requirement::POSITIVE_INT])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if ($product === null) {
            return $this->forward('App\Controller\ErrorController::index', [
                'title' => self::ERROR_TITLE,
                'message' => self::ERROR_SHOW,
                'id' => $id,
            ]);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

}
