<?php namespace App\Exception;

class TradeException extends \Exception {
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null) {
        if ('' === $message) {
            $message = 'Trade operation error';
        }

        return parent::__construct($message, $code, $previous);
    }
}
