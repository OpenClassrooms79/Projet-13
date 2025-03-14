<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ProductRepository $productRepository): Response
    {
        //dd($productRepository->findAll());
        return $this->render('main/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
}
