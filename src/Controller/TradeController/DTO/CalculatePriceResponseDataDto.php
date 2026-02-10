<?php namespace App\Controller\TradeController\DTO;

class CalculatePriceResponseDataDto {
    public function __construct(
        public readonly string $totalPrice,
    ) {
    }
}
