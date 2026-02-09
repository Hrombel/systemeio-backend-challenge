<?php namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PercentDiscountCoupon extends DiscountCoupon {
    #[ORM\Column(type: Types::SMALLINT)]
    private int $percentValue;

    public function getPercentValue(): int {
        return $this->percentValue;
    }

    public function setPercentValue(int $percentValue): self {
        $this->percentValue = $percentValue;

        return $this;
    }
}
