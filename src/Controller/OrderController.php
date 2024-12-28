<?php

namespace App\Controller;

use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order/{id}', name: 'order_show')]
    public function show(int $id, CartRepository $cartRepository): Response
    {
        // Récupère la commande (panier) par son ID
        $order = $cartRepository->find($id);

        // Si la commande n'existe pas ou n'est pas payée, lance une exception
        if (!$order || !$order->isStatus()) {
            throw $this->createNotFoundException('The order does not exist');
        }

        // Rend la vue de la commande avec les informations nécessaires
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}