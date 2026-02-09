<?php namespace App\Controller;

use App\Service\Payment\Gateway;
use App\Service\Trade\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TradeController extends AbstractController {
    #[Route('/calculate-price', name: 'app_trade_calculate_price', methods: 'POST')]
    public function calculatePrice(Request $req, Trade $trade): JsonResponse {
        $data = $req->toArray();

        $totalPrice = $trade->calculatePrice($data['product'], $data['taxNumber'], $data['couponCode']);

        return $this->json([
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/purchase', name: 'app_trade_purchase', methods: 'POST')]
    public function purchase(Request $req, Trade $trade, Gateway $paymentGateway): JsonResponse {
        $data = $req->toArray();

        $totalPrice = $trade->calculatePrice($data['product'], $data['taxNumber'], $data['couponCode']);

        $processor = $paymentGateway->getPaymentSystem($data['paymentProcessor']);
        $processor->process(number_format($totalPrice, 2, '.'));

        return $this->json([
            'message' => 'OK',
        ]);
    }
}
