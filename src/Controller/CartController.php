<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**     
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CartService
     */
    private $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart/{id<\d+>}/add", name="cart_add")
     */
    public function add($id, Request $request): RedirectResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit que vous essayez d'ajouter n'existe pas !");
        }

        $this->cartService->add($id);

        $this->addFlash('success', "Le produit \"{$product->getName()}\" a bien été ajouté au panier");


        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("cart_show");
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(): Response
    {
        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();

        return $this->render('cart/show.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }


    /**
     * @Route("/cart/{id<\d+>}/delete", name="cart_delete")
     */
    public function delete($id): RedirectResponse
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit que vous essayez de supprimer n'existe pas !");
        }

        $this->cartService->remove($id);

        $this->addFlash('success', "Le produit \"{$product->getName()}\" a bien été supprimé du panier");

        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/{id<\d+>}/decrement", name="cart_decrement")
     */
    public function decrement($id): RedirectResponse
    {

        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit que vous essayez d'enlever' n'existe pas !");
        }

        $this->cartService->decrement($id);

        return $this->redirectToRoute("cart_show");
    }
}
