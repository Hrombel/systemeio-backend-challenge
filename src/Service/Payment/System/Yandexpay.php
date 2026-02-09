<?php namespace App\Service\Payment\System;

use App\Exception\NotImplementedException;
use App\Service\Payment\PaymentSystem;

/**
 * To demonstrate the ability of disabling any of connected pay systems.
 */
class Yandexpay extends PaymentSystem {
    public function __construct() {
        throw new NotImplementedException('The Method is not implemented');
    }

    public function process(string $totalPrice) {
        throw new NotImplementedException('The Method is not implemented');
    }
}
