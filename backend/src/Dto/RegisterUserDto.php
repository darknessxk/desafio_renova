<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints;

class RegisterUserDto
{
    #[Constraints\NotBlank]
    #[Constraints\Email(
        message: 'The email "{{ value }}" is not a valid email.',
        mode: 'strict'
    )]
    private ?string $email = null;

    #[Constraints\NotBlank]
    #[Constraints\Length(min: 2, max: 64)]
    private ?string $firstName = null;

    #[Constraints\NotBlank]
    #[Constraints\Length(min: 2, max: 64)]
    private ?string $lastName = null;

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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
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
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'password' => $this->password,
        ];
    }

    public static function fromArray(array $data): self
    {
        return self::of(
            $data['email'],
            $data['firstName'],
            $data['lastName'],
            $data['password']
        );
    }

    static function of(string $email, string $firstName, string $lastName, string $password): self
    {
        $dto = new self();
        $dto->setEmail($email);
        $dto->setFirstName($firstName);
        $dto->setLastName($lastName);
        $dto->setPassword($password);

        return $dto;
    }
}