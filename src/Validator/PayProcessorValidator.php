<?php namespace App\Validator;

use App\Service\Payment\Gateway;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PayProcessorValidator extends ConstraintValidator {
    public function __construct(
        private readonly Gateway $paySystem,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void {
        /* @var PayProcessor $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $types = $this->paySystem->getPaySystemTypes();
        if (in_array($value, $types)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation()
        ;
    }
}
