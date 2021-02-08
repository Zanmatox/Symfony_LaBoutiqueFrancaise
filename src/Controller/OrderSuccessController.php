<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use App\Classe\Cart;
use App\Classe\Mail;

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
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFirstname()."<br/>Merci pour votre commande sur LaBoutique Française.<br><br/>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande La Boutique Française est bien validée.', $content);

        }
        
        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}
