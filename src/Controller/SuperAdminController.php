<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuperAdminController extends AbstractController
{
    #[Route('/super_admin/unpurchased-carts', name: 'superadmin_unpurchased_carts')]
    public function listUnpurchasedCarts(CartRepository $cartRepository): Response
    {
        // Rend la vue des paniers non achetés avec les informations nécessaires
        return $this->render('superadmin/unpurchased_carts.html.twig', [
            'unpurchasedCarts' => $cartRepository->findBy(['status' => false]),
        ]);
    }

    #[Route('/super_admin/recent-users', name: 'superadmin_recent_users')]
    public function listRecentUsers(EntityManagerInterface $entityManager): Response
    {
        // Récupère la date d'aujourd'hui à minuit
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        // Crée une requête pour récupérer les utilisateurs inscrits aujourd'hui
        $qb = $entityManager->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.createdAt >= :today')
            ->setParameter('today', $today)
            ->orderBy('u.createdAt', 'DESC');

        // Exécute la requête et récupère les résultats
        $recentUsers = $qb->getQuery()->getResult();

        // Rend la vue des utilisateurs inscrits aujourd'hui avec les informations nécessaires
        return $this->render('superadmin/recent_users.html.twig', [
            'recentUsers' => $recentUsers,
        ]);
    }
}