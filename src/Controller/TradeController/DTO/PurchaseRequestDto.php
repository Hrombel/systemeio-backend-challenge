<?php namespace App\Controller\TradeController\DTO;

use App\Validator\PayProcessor;
use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequestDto {
    use ProductDealTrait;

    #[Assert\NotBlank]
    #[PayProcessor]
    public readonly string $paymentProcessor;

    public function __construct(
        int $product,
        string $taxNumber,
        ?string $couponCode,
        string $paymentProcessor,
    ) {
        $this->product = $product;
        $this->taxNumber = $taxNumber;
        $this->couponCode = $couponCode;
        $this->paymentProcessor = $paymentProcessor;
    }
}
