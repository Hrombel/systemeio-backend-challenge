<?php namespace App\Service\Payment\System;

use App\Service\Payment\Exception\ProcessException;
use App\Service\Payment\PaymentSystem;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class Stripe extends PaymentSystem {
    private StripePaymentProcessor $processor;

    public function __construct() {
        $this->processor = new StripePaymentProcessor();
    }

    public function process(string $totalPrice): float {
        try {
            $value = self::convertPrice($totalPrice);
            $ok = $this->processor->processPayment($value);
            if (true !== $ok) {
                throw new \Exception('Unexpected payment result');
            }

            return $value;
        } catch (\Throwable $e) {
            throw new ProcessException('Error processing this request', 1, $e);
        }
    }

    public static function convertPrice(string $price): float {
        return floatval($price);
    }
}
