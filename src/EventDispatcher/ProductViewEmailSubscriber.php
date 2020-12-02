<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendProductViewEmail'
        ];
    }
    
    public function sendProductViewEmail(ProductViewEvent $productViewEvent)
    {
    }
}
