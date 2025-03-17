<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
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
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            // User is already logged in
            // TODO définir la route de redirection
            return $this->redirectToRoute('app_main');
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

            // TODO définir la route de redirection
            return $this->redirectToRoute('app_main');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/connexion', name: 'user_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // si on vient sur la page de connexion et que l'utilisateur est deja identifié
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            // TODO définir la route de redirection
            return $this->redirectToRoute('app_main');
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

        return $this->render('user/login.html.twig', [
            'form' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(), // dernier message d'erreur
        ]);
    }

    #[Route(path: '/deconnexion', name: 'user_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
