<?php namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase {
    public function testCalculatePrice(): void {
        $client = static::createClient();
        $crawler = $client->request(
            'POST', '/calculate-price',
            content: json_encode([
                'product' => 1,
                'taxNumber' => 'DE123456789',
                'couponCode' => 'P10',
            ])
        );

        $this->assertResponseIsSuccessful();
    }
}
