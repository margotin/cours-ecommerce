<?php

namespace App\Stripe;

use App\Entity\Purchase;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    private $publicKey;
    private $secretKey;

    public function __construct(string $publicKey, string $secretKey)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
    }



    public function getPaymentIntent(Purchase $purchase)
    {
        Stripe::setApiKey($this->secretKey);
        return PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'
        ]);
    }

    
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
