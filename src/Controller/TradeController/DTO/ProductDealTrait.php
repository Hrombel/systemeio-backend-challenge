<?php namespace App\Controller\TradeController\DTO;

use App\Validator\CouponCode;
use App\Validator\ProductId;
use App\Validator\TaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

trait ProductDealTrait {
    #[ProductId]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    public readonly int $product;

    #[Assert\NotBlank]
    #[TaxNumber]
    public readonly string $taxNumber;

    #[CouponCode(productField: 'product')]
    #[Assert\Length(min: 1)]
    public readonly ?string $couponCode;
}
