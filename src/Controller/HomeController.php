<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Rend la vue de la page d'accueil avec la liste des produits
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
}
