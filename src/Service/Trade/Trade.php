<?php namespace App\Service\Trade;

use App\Entity\DiscountCoupon as DiscountCouponEntity;
use App\Entity\FixedDiscountCoupon as FixedDiscountCouponEntity;
use App\Entity\PercentDiscountCoupon as PercentDiscountCouponEntity;
use App\Entity\Product as ProductEntity;
use App\Entity\Tax as TaxEntity;
use App\Exception\NotImplementedException;
use App\Service\Trade\Exception\InvalidCouponTradeException;
use App\Service\Trade\Exception\ProductNotFoundTradeException;
use App\Service\Trade\Exception\UnrecognizedTaxTradeException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class Trade {
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function couponExists(string $couponCode, int $sellerId): bool {

        $couponFQN = DiscountCouponEntity::class;
        return !!$this->em->createQuery(
           "SELECT COUNT(c)
            FROM $couponFQN c
            WHERE
                c.validUntil > CURRENT_TIMESTAMP() AND
                c.code = :couponCode AND
                c.sellerId = :sellerId
        ")
            ->setParameters([
                'couponCode' => $couponCode,
                'sellerId' => $sellerId,
            ])
            ->getSingleScalarResult()    
        ;
    }

    /**
     * TODO: add caching of returned array.
     *
     * @return string[]
     * */
    public function getTaxRules(): array {
        $taxFQN = TaxEntity::class;

        $rules = $this->em->createQuery(
            "SELECT t.rule
            FROM $taxFQN t
        ")
            ->getSingleColumnResult()
        ;

        return $rules;
    }

    public function calculatePrice(int $productId, string $taxNumber, ?string $couponCode = null): string {
        $productFQN = ProductEntity::class;
        $taxFQN = TaxEntity::class;
        $discountCouponFQN = DiscountCouponEntity::class;

        try {
            $foundItem = $this->em->createQuery(
                "SELECT
                    p.price productPrice, 
                    t.percentValue taxValuePercent,
                    c
                FROM
                    $productFQN p
                LEFT JOIN
                    $taxFQN t WITH SIMILAR_TO(:taxNumber, t.rule) = TRUE
                LEFT JOIN
                    $discountCouponFQN c WITH c.sellerId = p.sellerId AND c.code = :couponCode AND c.validUntil > CURRENT_TIMESTAMP()
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

        $price = $foundItem['productPrice'];
        $taxValuePercent = $foundItem['taxValuePercent'] ?? null;
        $coupon = $foundItem[0];

        if (!$taxValuePercent) {
            throw new UnrecognizedTaxTradeException('Invalid or unrecognized tax number given');
        }

        if (null !== $couponCode && null === $coupon) {
            throw new InvalidCouponTradeException('Specified coupon not found');
        }

        if (null !== $couponCode && !in_array(get_class($coupon), [FixedDiscountCouponEntity::class, PercentDiscountCouponEntity::class])) {
            throw new NotImplementedException('Unrecognized coupon type found');
        }

        if ($coupon instanceof FixedDiscountCouponEntity) {
            $couponArgs = [$coupon->getExactValue()];
        } elseif ($coupon instanceof PercentDiscountCouponEntity) {
            $couponArgs = [null, $coupon->getPercentValue()];
        } else {
            $couponArgs = [];
        }

        $totalPrice = $this->calculateTotalItemPrice(
            $price,
            $taxValuePercent,
            ...$couponArgs,
        );

        return $totalPrice;
    }

    /** TODO: make static. */
    public function calculateTotalItemPrice(
        string $itemPrice, int $taxValuePercent, ?string $couponExactValue = null, ?int $couponPercentValue = null,
    ): string {
        $totalPrice = $itemPrice;

        if ($couponExactValue) {
            $totalPrice = bcsub($itemPrice, $couponExactValue, 2);
        } elseif ($couponPercentValue) {
            $totalPrice = bcmul($itemPrice, (string) ((100 - $couponPercentValue) / 100), 2);
        }

        $taxValue = bcmul($totalPrice, (string) ($taxValuePercent / 100), 2);

        $totalPrice = bcadd($totalPrice, $taxValue, 2);

        return $totalPrice;
    }
}
