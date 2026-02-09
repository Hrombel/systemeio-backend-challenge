<?php namespace App\Service\Payment;

use App\Service\Payment\Contract\PaymentSystemInterface;

abstract class PaymentSystem implements PaymentSystemInterface {
    public function getType(): string {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }
}
