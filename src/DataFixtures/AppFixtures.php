<?php namespace App\DataFixtures;

use App\Entity\FixedDiscountCoupon;
use App\Entity\PercentDiscountCoupon;
use App\Entity\Product;
use App\Entity\Tax;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture {
    public function load(ObjectManager $manager): void {
        $this->addProducts([
            ['Iphone',      '100.00'],
            ['Headphones',   '20.00'],
            ['Case',         '10.00'],
        ],
            $manager);

        $this->addTaxes([
            ['DE', 19, 'DE\d{9}'],
            ['IT', 22, 'IT\d{11}'],
            ['GR', 24, 'GR\d{9}'],
            ['FR', 20, 'FR[A-Z]{2}\d{9}'],
        ],
            $manager);

        $this->addCoupons([
            ['P10',  '%10'],
            ['P100', '%100'],
            ['F10',  '10.00'],
            ['F05',  '0.5'],
        ],
            $manager);

        $manager->flush();
    }

    private function addCoupons(array $values, ObjectManager $manager) {
        $validUntil = new \DateTime();
        $validUntil->add(\DateInterval::createFromDateString('1 year'));
        foreach ($values as $v) {
            $coupon = '%' === $v[1][0]
                ? new PercentDiscountCoupon()
                : new FixedDiscountCoupon()
            ;
            $manager->persist($coupon);

            $coupon->setCode($v[0]);
            $coupon->setSellerId(1);
            $coupon->setValidUntil(clone $validUntil);

            if ($coupon instanceof PercentDiscountCoupon) {
                $coupon->setPercentValue((int) substr($v[1], 1));
            } else {
                $coupon->setExactValue($v[1]);
            }
        }
    }

    private function addTaxes(array $values, ObjectManager $manager) {
        foreach ($values as $v) {
            $tax = new Tax();
            $manager->persist($tax);

            $tax->setCountryCode($v[0]);
            $tax->setPercentValue($v[1]);
            $tax->setRule($v[2]);
        }
    }

    private function addProducts(array $values, ObjectManager $manager) {
        foreach ($values as $v) {
            $product = new Product();
            $manager->persist($product);

            $product->setName($v[0]);
            $product->setPrice($v[1]);
            $product->setSellerId(1);
        }
    }
}
