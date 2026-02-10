<?php namespace App\Tests\Service;

use App\Service\Trade\Exception\InvalidCouponTradeException;
use App\Service\Trade\Exception\ProductNotFoundTradeException;
use App\Service\Trade\Exception\UnrecognizedTaxTradeException;
use App\Service\Trade\Trade;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

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

    public static function calculatePriceProvider(): array {
        return [
            // valid
            [1, 'DE123456789', 'P10', '107.10'],
            [1, 'DE123456789',  null, '119.00'],

            // invalid
            [9999, 'DE1234567890', 'P10', '',  ProductNotFoundTradeException::class],
            [1, 'UNKNOWNTAX',  'P123', '', UnrecognizedTaxTradeException::class],
            [1, 'DE123456789',  'UNKNOWN', '', InvalidCouponTradeException::class],
            [1, 'DE123456789',  'OUTDATED', '', InvalidCouponTradeException::class],
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

    #[DataProvider('calculatePriceProvider')]
    public function testCalculatePrice(int $productId, string $taxNumber, ?string $couponCode, string $expectedPrice, ?string $expectedException = null): void {
        self::bootKernel();

        /** @var Trade $trade */
        $trade = static::getContainer()->get(Trade::class);

        try {
            $totalPrice = $trade->calculatePrice($productId, $taxNumber, $couponCode);
            $this->assertEquals($expectedPrice, $totalPrice, 'Incorrect total price');
        }
        catch(Throwable $e) {
            if(!$expectedException) {
                throw $e;
            }

            $this->assertInstanceOf($expectedException, $e);
        }
    }
}
