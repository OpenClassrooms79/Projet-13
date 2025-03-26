<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\User;
use App\Form\ApiAccessType;
use App\Form\ConfirmOrderType;
use App\Form\DeleteAccountType;
use App\Form\EmptyCartType;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {}

    #[Route('/inscription', name: 'user_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('user_account');
        }

        $user = new User();

        $form = $this->createForm(
            RegisterType::class,
            $user,
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO valeur à vérifier
            $form->get('cguAccepted')->getData();

            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword(),
            );

            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_account');
        }

        return $this->render('main/register.html.twig', [
            'form' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(), // dernier message d'erreur
        ]);
    }

    #[Route('/connexion', name: 'user_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // si on vient sur la page de connexion et que l'utilisateur est deja identifié
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('user_account');
        }

        // dernière adresse e-mail saisie dans le formulaire
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(
            LoginType::class,
            new User(),
            [
                'last_username' => $lastUsername,
            ],
        );

        return $this->render('main/login.html.twig', [
            'form' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(), // dernier message d'erreur
        ]);
    }

    #[Route(path: '/deconnexion', name: 'user_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/compte', name: 'user_account')]
    public function account(Request $request, Security $security): Response
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('user_login');
        }

        /** @var User $user */
        $user = $security->getUser();

        $apiForm = $this->createForm(ApiAccessType::class);
        $deleteForm = $this->createForm(DeleteAccountType::class);

        // activation ou désactivation de l'accès à l'API
        $apiForm->handleRequest($request);
        if ($apiForm->isSubmitted() && $apiForm->isValid()) {
            $user->setApiEnabled(!$user->isApiEnabled());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('user_account');
        }

        // suppression du compte
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            // suppresion de la session
            $this->security->logout();
            return $this->redirectToRoute('app_main'); // rediriger vers la page d'accueil
        }

        return $this->render('user/account.html.twig', [
            'apiForm' => $apiForm,
            'deleteForm' => $deleteForm,
            'orders' => $user->getOrders(),
        ]);
    }

    #[Route(path: '/panier', name: 'user_cart')]
    public function cart(Request $request, CartService $cartService, ProductRepository $productRepository): Response
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('user_login');
        }

        $empty_cart_form = $this->createForm(EmptyCartType::class);
        $confirm_order_form = $this->createForm(ConfirmOrderType::class);

        // vider le panier
        $empty_cart_form->handleRequest($request);
        if ($empty_cart_form->isSubmitted() && $empty_cart_form->isValid()) {
            $cartService->clear();
            return $this->redirectToRoute('user_cart');
        }

        // validation de la commande
        $confirm_order_form->handleRequest($request);
        if ($confirm_order_form->isSubmitted() && $confirm_order_form->isValid()) {
            // TODO transformer le contenu du panier en nouvelle commande
            $cart = $cartService->getCart();
            $order = new Order();
            $order->setUser($this->security->getUser());
            foreach ($cart as $productId => $quantity) {
                $product = $productRepository->find($productId);
                if ($product === null) {
                    continue; // au cas où un produit a été supprimé de la base de données
                }
                $orderDetail = new OrderDetail();
                $orderDetail->setProduct($product);
                $orderDetail->setQuantity($quantity);
                $order->addOrderDetail($orderDetail);
            }
            $order->recalculateTotal();
            $order->setOrderDate(new DateTime('now'));
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $cartService->clear(); // vider le panier une fois la commande a été créée
            return $this->redirectToRoute('user_account'); // afficher la liste des commandes
        }

        $cart = $cartService->getCart();
        $cartData = [];
        $total = 0;
        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product === null) {
                continue; // au cas où un produit a été supprimé de la base de données
            }


            $subtotal = $product->getPrice() * $quantity;
            $cartData[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }

        return $this->render('user/cart.html.twig', [
            'empty_cart_form' => $empty_cart_form,
            'confirm_order_form' => $confirm_order_form,
            'cartData' => $cartData,
            'total' => $total,
        ]);
    }
}