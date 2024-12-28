<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'account')]
    public function index(Request $request, EntityManagerInterface $entityManager, CartRepository $cartRepository): Response
    {
        // Récupère l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        // Crée un formulaire pour mettre à jour le profil de l'utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistre les modifications
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('account');
        }

        // Rend la vue du compte utilisateur avec les informations nécessaires
        return $this->render('account/index.html.twig', [
            'user' => $user,
            'orders' => $cartRepository->findBy(['user' => $user, 'status' => true]),
            'form' => $form->createView(),
        ]);
    }
}
