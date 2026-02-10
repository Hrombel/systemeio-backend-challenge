<?php namespace App\Validator;

use App\Repository\TaxRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TaxNumberValidator extends ConstraintValidator {
    public function __construct(
        private TaxRepository $tax,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void {
        /* @var TaxNumber $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        /**
         * TODO: index by country code to simplify further matching
         * TODO: add caching of returned array.
         */
        $taxRules = $this->tax->getTaxRules();

        foreach ($taxRules as $rule) {
            if (1 === preg_match("/^$rule$/", $value)) {
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
