<?php namespace App\Validator;

use App\Service\Trade\Trade;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TaxNumberValidator extends ConstraintValidator {


    public function __construct(
        private Trade $tax,
    ) { }

    public function validate(mixed $value, Constraint $constraint): void {
        /* @var TaxNumber $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        /** TODO: index by country code to simplify further matching */
        $taxRules = $this->tax->getTaxRules();

        foreach($taxRules as $rule) {
            if(preg_match("/^$rule$/", $value) === 1) {
                return;
            }
        }
        

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation()
        ;
    }
}
