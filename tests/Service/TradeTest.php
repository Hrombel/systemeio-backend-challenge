<?php namespace App\Tests\Service;

use App\Service\Trade\Trade;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * TODO: Add invalid test cases.
 */
class TradeTest extends KernelTestCase {
    public static function calculateTotalItemPriceValidProvider(): array {
        return [
            ['100.00', 14,   '5%', '108.30'],
            ['30.00', 14, '7.28',  '25.90'],
            ['83.00', 21,   null, '100.43'],
            ['100.00', 24,   '6%', '116.56'],
        ];
    }

    #[DataProvider('calculateTotalItemPriceValidProvider')]
    public function testCalculateTotalItemPrice(string $productPrice, int $taxValuePercent, ?string $couponValue, string $resultPrice): void {

        $couponArgs = [];
        if (null !== $couponValue) {
            if ('%' === substr($couponValue, -1, 1)) {
                $couponArgs = [null, substr($couponValue, 0, -1)];
            } else {
                $couponArgs = [$couponValue];
            }
        }

        $totalPrice = Trade::calculateTotalItemPrice($productPrice, $taxValuePercent, ...$couponArgs);

        $this->assertEquals($resultPrice, $totalPrice, 'Incorrect total price');
    }
}
