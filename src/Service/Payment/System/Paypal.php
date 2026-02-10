<?php namespace App\Service\Payment\System;

use App\Service\Payment\Exception\ProcessException;
use App\Service\Payment\PaymentSystem;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class Paypal extends PaymentSystem {
    private PaypalPaymentProcessor $processor;

    public function __construct() {
        $this->processor = new PaypalPaymentProcessor();
    }

    public function process(string $totalPrice): int {
        try {
            $value = self::convertPrice($totalPrice);
            $this->processor->pay($value);
            return $value;
        } catch (\Throwable $e) {
            throw new ProcessException('Error processing this request', 1, $e);
        }
    }

    public static function convertPrice(string $price): int {
        return intval(floatval($price) * 100);
    }
}
