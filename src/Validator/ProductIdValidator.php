<?php namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\Product as ProductEntity;

final class ProductIdValidator extends ConstraintValidator {
    
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }


    public function validate(mixed $value, Constraint $constraint): void {
        /* @var Product $constraint */

        if (null === $value || '' === $value) {
            return;
        }
        
        /** @var ProductEntity $product */
        $product = $this->em->find(ProductEntity::class, $value);
        if($product) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation()
        ;
    }
}
