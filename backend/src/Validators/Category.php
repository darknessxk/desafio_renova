<?php

namespace App\Validators;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Category extends Constraint
{
    public string $message = 'The category "{{ value }}" is not valid. Valid categories are: {{ categories }}';
    public string $mode = 'strict';

    public function __construct(?string $mode = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct(
            options: [],
            groups: $groups,
            payload: $payload
        );

        $this->mode = $mode ?? $this->mode;
        $this->message = $message ?? $this->message;
    }
}