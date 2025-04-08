<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private SessionInterface $session;
    private const CART_KEY = 'cart';

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function add(int $productId, int $quantity = 1): void
    {
        if ($quantity === 0) {
            $this->remove($productId);
            return;
        }
        $cart = $this->session->get(self::CART_KEY, []);
        $cart[$productId] = $quantity;
        $this->session->set(self::CART_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->session->get(self::CART_KEY, []);
        unset($cart[$productId]);
        $this->session->set(self::CART_KEY, $cart);
    }

    public function getQuantity(int $productId): int
    {
        $cart = $this->session->get(self::CART_KEY, []);
        return $cart[$productId] ?? 0;
    }

    public function getCart(): array
    {
        return $this->session->get(self::CART_KEY, []);
    }

    public function clear(): void
    {
        $this->session->remove(self::CART_KEY);
    }
}