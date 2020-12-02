<?php

namespace App\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    private $security;
    private $cartService;
    private $entityManager;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }

    public function storePurchase(Purchase $purchase)
    {
        $user =  $this->security->getUser();
        $purchase->setUser($user);

        $this->entityManager->persist($purchase);

        $cartItems = $this->cartService->getDetailedCartItems();
        foreach ($cartItems as $cartItem) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->getProduct())
                ->setProductName($cartItem->getProduct()->getName())
                ->setProductPrice($cartItem->getProduct()->getPrice())
                ->setQuantity($cartItem->getQuantity())
                ->setTotal($cartItem->getTotal());

            $this->entityManager->persist($purchaseItem);
        }

        $this->entityManager->flush();
    }
}
