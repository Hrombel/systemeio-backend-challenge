<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity]
#[Index(fields: ['rule'])]
class Tax {
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $countryCode = null; // Alpha2

    #[ORM\Column(length: 255)]
    private string $rule;

    #[ORM\Column]
    private ?int $percentValue = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getCountryCode(): ?string {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getRule(): string {
        return $this->rule;
    }

    public function setRule(string $rule): static {
        $this->rule = $rule;

        return $this;
    }

    public function getPercentValue(): ?int {
        return $this->percentValue;
    }

    public function setPercentValue(int $percentValue): static {
        $this->percentValue = $percentValue;

        return $this;
    }
}
