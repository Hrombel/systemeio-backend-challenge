<?php namespace App\Service\Trade;

use App\Entity\DiscountCoupon as DiscountCouponEntity;
use App\Entity\FixedDiscountCoupon as FixedDiscountCouponEntity;
use App\Entity\PercentDiscountCoupon as PercentDiscountCouponEntity;
use App\Entity\Product as ProductEntity;
use App\Entity\Tax as TaxEntity;
use App\Exception\NotImplementedException;
use App\Service\Trade\Exception\ProductNotFoundTradeException;
use App\Service\Trade\Exception\UnrecognizedTaxTradeException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class Trade {

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function calculatePrice(int $productId, string $taxNumber, string $couponCode): float {
        $productFQN = ProductEntity::class;
        $taxFQN = TaxEntity::class;
        $discountCouponFQN = DiscountCouponEntity::class;

        try {
            $foundItem = $this->em->createQuery(
                "SELECT
                    p.price productPrice, 
                    t.percentValue taxValue,
                    c
                FROM
                    $productFQN p
                LEFT JOIN
                    $taxFQN t WITH SIMILAR_TO(:taxNumber, t.rule) = TRUE
                LEFT JOIN
                    $discountCouponFQN c WITH c.sellerId = p.sellerId AND c.code = :couponCode
                WHERE
                    p.id = :productId AND p.price IS NOT NULL
            ")
                ->setParameters([
                    'productId' => $productId,
                    'taxNumber' => $taxNumber,
                    'couponCode' => $couponCode,
                ])
                ->getSingleResult()
            ;
        } catch (NoResultException) {
            throw new ProductNotFoundTradeException('Product not found by productId given');
        }

        $price = (float) $foundItem['productPrice'];
        $taxValue = $foundItem['taxValue'] ?? null;
        $coupon = $foundItem[0];

        if (!$taxValue) {
            throw new UnrecognizedTaxTradeException('Invalid or unrecognized tax number given');
        }

        if (null !== $coupon && !in_array(get_class($coupon), [FixedDiscountCouponEntity::class, PercentDiscountCouponEntity::class])) {
            throw new NotImplementedException('Unrecognized coupon type found');
        }

        if ($coupon instanceof FixedDiscountCouponEntity) {
            $price -= $coupon->getExactValue();
        } elseif ($coupon instanceof PercentDiscountCouponEntity) {
            $price *= (100 - $coupon->getPercentValue()) / 100;
        }

        $price += $taxValue;

        return $price;
    }

}