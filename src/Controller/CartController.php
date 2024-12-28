<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_show')]
    public function show(CartRepository $cartRepository): Response
    {
        // Récupère l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Rend la vue du panier avec les informations nécessaires
        return $this->render('cart/show.html.twig', [
            'cart' => $cartRepository->findOneBy(['user' => $user, 'status' => false]),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST', 'GET'])]
    public function addToCart(int $id, EntityManagerInterface $entityManager, CartRepository $cartRepository, ProductRepository $productRepository): Response
    {
        // Récupère le produit à ajouter au panier par son ID
        $product = $productRepository->find($id);

        // Si le produit n'existe pas, ajoute un message flash d'erreur et redirige vers la page d'accueil
        if (!$product) {
            $this->addFlash('error', 'The product does not exist');
            return $this->redirectToRoute('home');
        }

        // Récupère l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Si l'utilisateur n'est pas connecté, ajoute un message flash d'erreur et redirige vers la page de connexion
        if (!$user) {
            $this->addFlash('error', 'You need to be logged in to add products to the cart');
            return $this->redirectToRoute('login');
        }

        // Récupère le panier en cours de l'utilisateur (non payé) ou en crée un nouveau si aucun n'existe
        $cart = $cartRepository->findOneBy(['user' => $user, 'status' => false]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setPurchaseDate(new \DateTime());
            $cart->setStatus(false);
            $entityManager->persist($cart);
        }

        // Récupère l'élément de panier existant ou en crée un nouveau
        $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy(['cart' => $cart, 'product' => $product]);
        if ($cartItem) {
            // Si l'élément de panier existe, augmente la quantité
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            // Sinon, crée un nouvel élément de panier avec une quantité par défaut de 1
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setCart($cart);
            $cartItem->setQuantity(1);
            $cartItem->setDate(new \DateTime());
            $entityManager->persist($cartItem);
        }

        // Enregistre les modifications dans la base de données
        $entityManager->flush();

        // Ajoute un message flash de succès et redirige vers la page du panier
        $this->addFlash('success', 'Product added to cart successfully!');
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(int $id, EntityManagerInterface $entityManager, CartItemRepository $cartItemRepository): Response
    {
        // Récupère l'élément de panier à supprimer par son ID
        $cartItem = $cartItemRepository->find($id);

        // Si l'élément de panier existe, le supprimer de la base de données
        if ($cartItem) {
            $entityManager->remove($cartItem);
            $entityManager->flush();
        }

        // Redirige vers la page du panier après suppression
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/checkout/{id}', name: 'cart_checkout', methods: ['POST'])]
    public function checkout(int $id, EntityManagerInterface $entityManager, CartRepository $cartRepository): Response
    {
        // Récupère le panier à valider par son ID
        $cart = $cartRepository->find($id);

        // Si le panier existe, mettre à jour son statut à "payé"
        if ($cart) {
            $cart->setStatus(true);
            $entityManager->flush();
        }

        // Redirige vers la page du panier après validation
        return $this->redirectToRoute('cart_show');
    }
}
