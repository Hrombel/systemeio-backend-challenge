<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\InheritanceType;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'type', type: 'string')]
#[DiscriminatorMap(['discount-fixed' => FixedDiscountCoupon::class, 'discount-percent' => PercentDiscountCoupon::class])]
#[Index(fields: ['sellerId', 'code'])]
class Coupon {
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $code;

    #[ORM\Column]
    private int $sellerId;

    #[ORM\Column]
    private \DateTime $validUntil;

    public function getId(): int {
        return $this->id;
    }

    public function getSellerId(): int {
        return $this->sellerId;
    }

    public function setSellerId(int $sellerId): static {
        $this->sellerId = $sellerId;

        return $this;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function setCode(string $code): static {
        $this->code = $code;

        return $this;
    }

    public function getValidUntil(): \DateTime {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTime $validUntil): static {
        $this->validUntil = $validUntil;

        return $this;
    }
}
