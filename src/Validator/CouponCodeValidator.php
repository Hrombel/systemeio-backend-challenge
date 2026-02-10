<?php namespace App\Validator;

use App\Entity\Product as ProductEntity;
use App\Service\Trade\Trade;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\CouponCode;
use Doctrine\ORM\EntityManagerInterface;

final class CouponCodeValidator extends ConstraintValidator {
    
    public function __construct(
        private readonly Trade $trade,
        private readonly EntityManagerInterface $em,
    ) {
    }


    /**
     * @param CouponCode $constraint 
     * */
    public function validate(mixed $value, Constraint $constraint): void {

        if (null === $value || '' === $value) {
            return;
        }
        
        $obj = $this->context->getObject();
        $productId = $obj->{$constraint->productField};
        
        /** @var ProductEntity $product */
        $product = $this->em->find(ProductEntity::class, $productId);

        $sellerId = $product->getSellerId();

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
