<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
// use Twig\Environment;
// use Symfony\Component\Security\Core\Security;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// class PurchasesListController
class PurchasesListController extends AbstractController
{

    // private $security;    
    // private $twig;

    // public function __construct(Security $security, Environment $twig)
    // {
    //     $this->security = $security;
    //     $this->twig = $twig;
    // }

    // /**
    //  * @Route("/purchases", name="purchase_index")
    //  */
    // public function index(): Response
    // {
    //     /** @var User */
    //     $user = $this->security->getUser();
    //     if (!$user) {
    //         throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes");
    //     }

    //     $html = $this->twig->render('purchase/index.html.twig', [
    //         'purchases' => $user->getPurchases()
    //     ]);

    //     return new Response($html);
    // }


    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
     */
    public function index(): Response
    {
        /** @var User */
        $user = $this->getUser();

        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}
