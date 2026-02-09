<?php namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class FixedDiscountCoupon extends DiscountCoupon {
    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 2)]
    private string $exactValue;

    public function getExactValue(): string {
        return $this->exactValue;
    }

    public function setExactValue(string $exactValue): self {
        $this->exactValue = $exactValue;

        return $this;
    }
}
