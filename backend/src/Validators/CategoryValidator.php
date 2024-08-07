<?php

namespace App\Validators;

use App\Config\Categories;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CategoryValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Category) {
            throw new UnexpectedTypeException($constraint, Category::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $categories = array_column(Categories::cases(), 'value');

        if ($constraint->mode === 'strict') {
            $this->strict($value, $categories, $constraint);
        } else {
            $this->loose($value, $categories, $constraint);
        }
    }

    private function strict(string $value, array $categories, Category $constraint): void
    {
        if (!in_array($value, $categories)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->setParameter('{{ categories }}', implode(', ', $categories))
                ->addViolation();
        }
    }

    private function loose(string $value, array $categories, Category $constraint): void
    {
        $value = strtolower($value);
        $categories = array_map('strtolower', $categories);

        if (!in_array($value, $categories)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->setParameter('{{ categories }}', implode(', ', $categories))
                ->addViolation();
        }
    }
}