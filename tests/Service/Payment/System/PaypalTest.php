<?php namespace App\Tests\Service\Payment\System;

use App\Service\Payment\System\Paypal;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaypalTest extends KernelTestCase {
    public static function converterProvider(): array {
        return [
            [ '100.00', 10000 ],
            [ '150.40', 15040 ],
            [ '999.00', 99900 ],
        ];
    }

    #[DataProvider('converterProvider')]
    public function testConverter(string $price, int $expectedPrice): void {

        $resultPrice = Paypal::convertPrice($price);

        $this->assertEquals($expectedPrice, $resultPrice);
    }
}
