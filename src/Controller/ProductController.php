<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductUpdateType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route("/products", name: 'products', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupère tous les produits à partir du repository
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}', name: 'product_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        // Récupère le produit par son ID
        $product = $productRepository->find($id);

        // Si le produit n'existe pas, lance une exception
        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }

        // Rend la vue du produit avec les informations nécessaires
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/new', name: 'product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Crée un nouvel objet Product
        $product = new Product();
        // Crée un formulaire pour le produit
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistre le produit dans la base de données
        if ($form->isSubmitted() && $form->isValid()) {
            // Gère le téléchargement de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Impossible d'ajouter l'image");
                    return $this->redirectToRoute('app_product');
                }

                $product->setImage($newFilename);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully!');
            return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
        }

        // Rend la vue de création de produit avec le formulaire
        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Crée un formulaire pour le produit
        $form = $this->createForm(ProductUpdateType::class, $product);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistre les modifications dans la base de données
        if ($form->isSubmitted() && $form->isValid()) {
            // Gère le téléchargement de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
 
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Impossible d'ajouter l'image");
                    return $this->redirectToRoute('app_product');
                }
 
                $product->setImage($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
        }

        // Rend la vue de modification de produit avec le formulaire
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('product/{id}', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Vérifie le token CSRF pour la suppression
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);

            $this->addFlash('danger', 'Product deleted successfully!');
            $entityManager->flush();
        }

        return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
    }
}