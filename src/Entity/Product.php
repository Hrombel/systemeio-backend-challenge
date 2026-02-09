<?php namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity]
class Product {
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $sellerId;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 2, nullable: true)]
    private ?string $price = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getSellerId(): int {
        return $this->sellerId;
    }

    public function setSellerId(int $sellerId): static {
        $this->sellerId = $sellerId;

        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string {
        return $this->price;
    }

    public function setPrice(?string $price): static {
        $this->price = $price;

        return $this;
    }
}
