<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints;

class LoginDto
{
    #[Constraints\NotBlank]
    #[Constraints\Email(
        message: 'The email "{{ value }}" is not a valid email.',
        mode: 'strict'
    )]
    private ?string $email = null;

    #[Constraints\NotBlank]
    #[Constraints\Length(min: 6, max: 24)]
    private ?string $password = null;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    public static function fromArray(array $data): self
    {
        return self::of(
            $data['email'],
            $data['password']
        );
    }

    static function of(string $email, string $password): self
    {
        $dto = new self();
        $dto->setEmail($email);
        $dto->setPassword($password);

        return $dto;
    }
}