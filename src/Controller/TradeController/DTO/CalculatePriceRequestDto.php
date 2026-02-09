<?php namespace App\Controller\TradeController\DTO;

class CalculatePriceRequestDto {
    use ProductDealTrait;


    public function __construct(
        int $product,
        string $taxNumber,
        ?string $couponCode,
    ) {
        $this->product = $product;
        $this->taxNumber = $taxNumber;
        $this->couponCode = $couponCode;
    }
}