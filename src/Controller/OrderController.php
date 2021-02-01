<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\OrderType;
use App\Classe\Cart;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;


class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    { 
        if (!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getuser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form ->createView(),
            'cart' => $cart->getFull()
        ]);
    }
    
    /**
     * @Route("/commande/recapitulatif", name="order_recap")
     */
    public function add(Cart $cart, Request $request): Response
    { 

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getuser()
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>'.$delivery->getPhone();
            if ($delivery->getCompany())
            {
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }
            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();


            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            foreach ($cart->getFull() as $product) {
                dd($product);
            }



        }

        return $this->render('order/add.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }
}  
