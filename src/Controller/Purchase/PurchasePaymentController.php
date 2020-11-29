<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Stripe\StripeService;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{

    /** @var StripeService */
    private $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * @Route("/purchase/{id<\d+>}/pay", name="purchase_payment_form") 
     * @IsGranted("ROLE_USER")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            return $this->redirectToRoute('cart_show');
        }

        $paymentIntent = $this->stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'purchase_id' => $id,
            'stripePublicKey' => $this->stripeService->getPublicKey()
        ]);
    }
}
