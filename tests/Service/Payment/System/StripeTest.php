<?php namespace App\Tests\Service\Payment\System;

use App\Service\Payment\System\Stripe;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class StripeTest extends KernelTestCase {
    public static function converterProvider(): array {
        return [
            [ '100.00', 100.0 ],
            [ '150.20', 150.2 ],
            [ '999.30', 999.3 ],
        ];
    }

    #[DataProvider('converterProvider')]
    public function testConverter(string $price, float $expectedPrice): void {

        $resultPrice = Stripe::convertPrice($price);

        $this->assertEquals($expectedPrice, $resultPrice);
    }
}
