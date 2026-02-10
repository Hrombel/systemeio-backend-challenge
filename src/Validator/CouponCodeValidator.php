<?php namespace App\Validator;

use App\Service\Trade\Trade;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CouponCodeValidator extends ConstraintValidator {
    
    public function __construct(
        private readonly Trade $trade,
    ) {
    }


    public function validate(mixed $value, Constraint $constraint): void {
        /* @var Coupon $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $sellerId = 1;

        if($this->trade->couponExists($value, $sellerId)) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation()
        ;
    }
}
