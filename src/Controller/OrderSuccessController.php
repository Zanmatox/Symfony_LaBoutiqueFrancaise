<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use App\Classe\Cart;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
 
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        
        if (!$order->getIsPaid()) {
        // Vider la session "cart"
        $cart->remove();


        // Modifier le statut isPaid de ma commande en mettant 1
        $order->setIsPaid(1);
        $this->entityManager->flush();
        
        // Envoyer un mail au client pour confimer la commande
        }
        
        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}
