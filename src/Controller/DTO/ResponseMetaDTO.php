<?php namespace App\Controller\DTO;

class ResponseMetaDTO {
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
    ) {
    }
}
