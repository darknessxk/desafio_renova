<?php

namespace App\Dto;

use App\Config\Categories;
use App\Validators as AppValidator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Constraints;

class UpdateProjectDto
{
    #[Constraints\Length(min: 3, max: 255)]
    private ?string $name = null;

    #[Constraints\Length(min: 3, max: 2048)]
    private ?string $description = null;

    #[Constraints\Positive]
    private ?float $meta = null;

    #[AppValidator\Category(mode: 'loose')]
    private ?string $category = "";

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getMeta(): ?float
    {
        return $this->meta;
    }

    public function setMeta(float $meta): void
    {
        $this->meta = $meta;
    }

    public function getCategory(): ?Categories
    {
        return Categories::tryFrom($this->category);
    }

    public function setCategory(string $category): void
    {
        $category = Categories::tryFrom($category);

        if ($category === null) {
            throw new BadRequestException('Invalid category');
        }

        $this->category = $category->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'meta' => $this->meta,
            'category' => $this->category,
        ];
    }

    public static function fromArray(array $data): self
    {
        return self::of($data['name'], $data['description'], $data['meta'], $data['category']);
    }

    static function of(string $name, string $description, float $meta, string $category): self
    {
        $dto = new self();
        $dto->setName($name);
        $dto->setDescription($description);
        $dto->setMeta($meta);
        $dto->setCategory($category);

        return $dto;
    }
}