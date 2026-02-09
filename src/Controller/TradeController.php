<?php namespace App\Controller;

use App\Controller\TradeController\DTO\CalculatePriceRequestDto;
use App\Controller\TradeController\DTO\PurchaseRequestDto;
use App\Service\Payment\Gateway;
use App\Service\Trade\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class TradeController extends AbstractController {
    #[Route('/calculate-price', name: 'app_trade_calculate_price', methods: 'POST')]
    public function calculatePrice(
        #[MapRequestPayload] CalculatePriceRequestDto $data,
        Trade $trade
    ): JsonResponse {
        $totalPrice = $trade->calculatePrice($data->product, $data->taxNumber, $data->couponCode);

        return $this->json([
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/purchase', name: 'app_trade_purchase', methods: 'POST')]
    public function purchase(
        #[MapRequestPayload] PurchaseRequestDto $data,
        Trade $trade, 
        Gateway $paymentGateway
    ): JsonResponse {

        $totalPrice = $trade->calculatePrice($data->product, $data->taxNumber, $data->couponCode);

        $processor = $paymentGateway->getPaymentSystem($data->paymentProcessor);
        $processor->process($totalPrice);

        return $this->json([
            'message' => 'OK',
        ]);
    }
}
