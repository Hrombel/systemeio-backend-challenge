<?php namespace App\Service\Payment\System;

use App\Service\Payment\Exception\ProcessException;
use App\Service\Payment\PaymentSystem;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class Paypal extends PaymentSystem {
    private PaypalPaymentProcessor $processor;

    public function __construct() {
        $this->processor = new PaypalPaymentProcessor();
    }

    public function process(string $totalPrice) {
        try {
            $this->processor->pay(intval(floatval($totalPrice) * 100));
        } catch (\Throwable $e) {
            throw new ProcessException('Error processing this request', 1, $e);
        }
    }
}
