<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints;

class IsInUseDto
{
    #[Constraints\NotBlank]
    #[Constraints\Email(
        message: 'The email "{{ value }}" is not a valid email.',
        mode: 'strict'
    )]
    private ?string $email = null;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
        ];
    }

    public static function fromArray(array $data): self
    {
        return self::of($data['email']);
    }

    static function of(string $email): self
    {
        $dto = new self();
        $dto->setEmail($email);

        return $dto;
    }
}