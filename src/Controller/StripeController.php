<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use App\Classe\Cart;
use Symfony\Component\HttpFoundation\JsonResponse;


class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart): Response
    {

        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';


        foreach ($cart->getFull() as $product) {
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
        }
        Stripe::setApiKey('sk_test_51IGA0AFEv7D5M8waK1aF9uCDD69PuyhFnawp188koOFh8UlvU4zWW5TxJQfSK4oO3in47HwqFVYRMloZIfqbx2c400jSBouHdf');

            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    $product_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success.html',
                'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
            ]);

            $response = new JsonResponse(['id' => $checkout_session->id]);
            return $response;
    }
}
 