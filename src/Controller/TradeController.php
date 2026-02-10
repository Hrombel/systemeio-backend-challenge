<?php namespace App\Controller;

use App\Controller\DTO\ResponseDTO;
use App\Controller\DTO\ResponseMetaDTO;
use App\Controller\TradeController\DTO\CalculatePriceRequestDto;
use App\Controller\TradeController\DTO\CalculatePriceResponseDataDto;
use App\Controller\TradeController\DTO\PurchaseRequestDto;
use App\Service\Payment\Gateway;
use App\Service\Trade\Exception\TradeException;
use App\Service\Trade\Trade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class TradeController extends AbstractController {
    #[Route('/calculate-price', name: 'app_trade_calculate_price', methods: 'POST')]
    public function calculatePrice(
        #[MapRequestPayload] CalculatePriceRequestDto $data,
        Trade $trade,
    ): JsonResponse {
        try {
            $totalPrice = $trade->calculatePrice($data->product, $data->taxNumber, $data->couponCode);
        } catch (TradeException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        /** @var ResponseDTO<CalculatePriceResponseDataDto> $response */
        $response = new ResponseDTO(
            meta: new ResponseMetaDTO(success: true),
            data: new CalculatePriceResponseDataDto(totalPrice: $totalPrice),
        );

        return $this->json($response);
    }

    /**
     * TODO: Limit coupon usage with usage quantity field.
     */
    #[Route('/purchase', name: 'app_trade_purchase', methods: 'POST')]
    public function purchase(
        #[MapRequestPayload] PurchaseRequestDto $data,
        Trade $trade,
        Gateway $paymentGateway,
    ): JsonResponse {
        try {
            $totalPrice = $trade->calculatePrice($data->product, $data->taxNumber, $data->couponCode);
        } catch (TradeException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $processor = $paymentGateway->getPaymentSystem($data->paymentProcessor);
        $processor->process($totalPrice);

        return $this->json(
            new ResponseDTO(
                new ResponseMetaDTO(success: true)
            )
        );
    }
}
