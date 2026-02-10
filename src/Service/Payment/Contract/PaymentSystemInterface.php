<?php namespace App\Service\Payment\Contract;

interface PaymentSystemInterface {
    public function process(string $totalPrice): mixed;

    public function getType(): string;

    public static function convertPrice(string $price): mixed;
}
