<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints;

class CreateDonationDto
{
    #[Constraints\Length(max: 255)]
    public string $origin = "Anonymous";

    #[Constraints\NotBlank]
    #[Constraints\Type('float')]
    #[Constraints\Positive]
    public float $value;

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [
            'origin' => $this->origin,
            'value' => $this->value,
        ];
    }

    public static function fromArray(array $data): self
    {
        return self::of($data['origin'], $data['value']);
    }

    public static function of(string $origin, float $value): self
    {
        $dto = new self();
        $dto->origin = $origin;
        $dto->value = $value;

        return $dto;
    }
}
