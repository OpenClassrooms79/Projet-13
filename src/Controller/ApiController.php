<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{
    #[Route('/api/products', name: 'app_products', methods: ['GET'])]
    public function products(ProductRepository $productRepository): JsonResponse
    {
        return $this->json($productRepository->findAll());
    }
}
