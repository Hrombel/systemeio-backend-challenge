<?php namespace App\Exception;

class Exception extends \Exception {
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null) {
        if ('' === $message) {
            $message = 'Payment processing exception';
        }

        return parent::__construct($message, $code, $previous);
    }
}
