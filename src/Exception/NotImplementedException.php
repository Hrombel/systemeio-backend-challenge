<?php namespace App\Exception;

class NotImplementedException extends \Exception {
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null) {
        if ('' === $message) {
            $message = 'This logic is not implemented';
        }

        return parent::__construct($message, $code, $previous);
    }
}
