<?php namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase {
    public static function calculatePriceProvider(): array {
        return [
            // valid
            [1, 'DE123456789', 'P10',   200, '107.10'],
            [1, 'DE123456789',  null,   200, '119.00'],

            // invalid
            [1, 'DE1234567890', 'P10',  422],
            [1, 'DE123456789',  'P123', 422],
            [9999, 'DE123456789',  'P123', 422],
            [1, 'DE123456789',  'OLD', 422],
        ];
    }

    public static function purchaseProvider(): array {
        return [
            // valid
            [2, 'IT12345678900', 'P100', 'paypal',    200],
            [1, 'IT12345678900', null, 'stripe',    200],
            // invalid
            [2, 'IT12345678900', 'INVALID', 'paypal',    422],
            [2, 'IT12345678900', 'P100', 'PayPal',    422],
            [2, 'IT12345678900', 'P100', 'yandexpay', 422],
        ];
    }

    #[DataProvider('calculatePriceProvider')]
    public function testCalculatePrice(int $productId, string $taxNumber, ?string $couponCode, int $expectedCode, ?string $expectedPrice = null): void {
        $client = static::createClient();
        $client->request(
            'POST', '/calculate-price',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            content: json_encode([
                'product' => $productId,
                'taxNumber' => $taxNumber,
                'couponCode' => $couponCode,
            ])
        );

        $this->assertResponseStatusCodeSame($expectedCode);
        $content = $client->getResponse()->getContent() ?: '';
        $this->assertJson($content);

        $success = $expectedCode >= 200 && $expectedCode < 300;
        $data = json_decode($content, true);

        $this->assertSame($success, $data['meta']['success']);
        if ($success) {
            $this->assertSame($expectedPrice, $data['data']['totalPrice']);
        } else {
            $this->assertArrayHasKey('message', $data['meta']);
        }
    }

    #[DataProvider('purchaseProvider')]
    public function testPurchase(int $productId, string $taxNumber, ?string $couponCode, string $paymentProcessor, int $expectedCode): void {
        $client = static::createClient();
        $client->request(
            'POST', '/purchase',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            content: json_encode([
                'product' => $productId,
                'taxNumber' => $taxNumber,
                'couponCode' => $couponCode,
                'paymentProcessor' => $paymentProcessor,
            ])
        );

        $this->assertResponseStatusCodeSame($expectedCode);
        $content = $client->getResponse()->getContent() ?: '';
        $this->assertJson($content);

        $success = $expectedCode >= 200 && $expectedCode < 300;
        $data = json_decode($content, true);

        $this->assertSame($success, $data['meta']['success']);

        if (!$success) {
            $this->assertArrayHasKey('message', $data['meta']);
        }
    }
}
