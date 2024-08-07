<?php

namespace App\ArgumentResolver;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class QueryParamValueResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) return [];
        $argumentName = $argument->getName();
        $type = $argument->getType();
        $nullable = $argument->isNullable();

        $attr = $argument->getAttributes(QueryParam::class)[0];

        $name = $attr->getName() ?? $argumentName;
        $required = $attr->isRequired() ?? false;

        $value = $request->query->all($name);

        if (!$value && $argument->hasDefaultValue()) {
            $value = $argument->getDefaultValue();
        }

        if ($required && !$value) {
            throw new BadRequestException("Query parameter '$name' is required");
        }

        if (in_array($type, ['int', 'float', 'bool', 'string']) && is_array($value)) {
            $value = $value[0] ?? $value;
        }

        yield match ($type) {
            'int' => $value ? (int)$value : 0,
            'float' => $value ? (float)$value : .0,
            'bool' => (bool)$value,
            'string' => $value ? (string)$value : ($nullable ? null : ''),
            'array' => $value ? (array)$value : [],
            null => null
        };
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return count($argument->getAttributes(QueryParam::class)) > 0;
    }
}