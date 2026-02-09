<?php namespace App\Service\Payment\Contract;

interface PaymentSystemInterface {
    public function process(string $totalPrice);

    public function getType(): string;
}
