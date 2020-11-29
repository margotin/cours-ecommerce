<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var CartService */
    private $cartService;

    public function __construct(EntityManagerInterface $entityManager, CartService $cartService)
    {
        $this->entityManager = $entityManager;
        $this->cartService = $cartService;
    }

    /** 
     * @Route("/purchase/{id<\d+>}/terminate", name="purchase_payment_success") 
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);
        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash("warning", "La commande n'existe pas");
            return $this->redirectToRoute('purchase_index');
        }

        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->entityManager->flush();
        $this->cartService->empty();

        $this->addFlash("success", "la commande a été confirmée et payée !");
        return $this->redirectToRoute('purchase_index');
    }
}
