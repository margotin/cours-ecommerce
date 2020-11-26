<?php

namespace App\Cart;

use App\Entity\Product;

class CartItem
{
    private $product;
    private $quantity;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getTotal(): int
    {
        return $this->product->getPrice() * $this->quantity;
    }

   
    public function getProduct(): Product
    {
        return $this->product;
    }

    
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
