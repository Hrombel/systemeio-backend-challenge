<?php namespace App\Tests\Service\Payment;

use App\Service\Payment\Gateway;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class PaymentSystemTest extends KernelTestCase {
    public static function paymentSystemPriceConsistencyProvider(): array {
        return [
            [ '100.00'],
            [ '150.00'],
            [ '999.00'],
        ];
    }

    #[DataProvider('paymentSystemPriceConsistencyProvider')]
    public function testPaymentSystemPriceConsistency(string $price): void {
        self::bootKernel();

        /** @var Gateway $gw */
        $gw = static::getContainer()->get(Gateway::class);

        foreach($gw->getPaySystemTypes() as $type) {
            $ps = $gw->getPaymentSystem($type);

            $convertedPrice = $ps::convertPrice($price);
            $processedPrice = $ps->process($price);

            $this->assertEquals($convertedPrice, $processedPrice, 'Converted and processed prices must be equal');
        }
    }
}
