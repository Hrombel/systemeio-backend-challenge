<?php namespace App\Controller\DTO;

/**
 * @template T of object
 */
class ResponseDTO {
    /**
     * @param T $data
     */
    public function __construct(
        public readonly ResponseMetaDTO $meta,
        public readonly ?object $data = null,
    ) {
    }
}
