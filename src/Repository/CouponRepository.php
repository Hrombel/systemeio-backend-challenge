<?php namespace App\Repository;

use App\Entity\Coupon;
use App\Entity\DiscountCoupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupon>
 */
class CouponRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Coupon::class);
    }

    public function couponExists(string $couponCode, int $sellerId): bool {
        $couponFQN = DiscountCoupon::class;

        return (bool) $this->getEntityManager()->createQuery(
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
}
