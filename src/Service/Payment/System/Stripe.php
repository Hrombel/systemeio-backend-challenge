<?php namespace App\Service\Payment\System;

use App\Service\Payment\Exception\ProcessException;
use App\Service\Payment\PaymentSystem;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class Stripe extends PaymentSystem {
    private StripePaymentProcessor $processor;

    public function __construct() {
        $this->processor = new StripePaymentProcessor();
    }

    public function process(string $totalPrice) {
        try {
            $ok = $this->processor->processPayment(floatval($totalPrice));
            if (true !== $ok) {
                throw new \Exception('Unexpected payment result');
            }
        } catch (\Throwable $e) {
            throw new ProcessException('Error processing this request', 1, $e);
        }
    }
}
