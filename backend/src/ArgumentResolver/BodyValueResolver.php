<?php

namespace App\ArgumentResolver;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BodyValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) { }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($request, $argument)) {
            return [];
        }

        if ($request->getContentTypeFormat() !== 'json') {
            $content = json_encode($request->request->all());
        } else {
            $content = $request->getContent();
        }

        $data = $this->serializer->deserialize($content, $argument->getType(), 'json');

        if ($argument->getAttributes(Body::class)[0]->validate) {
            $errors = $this->validator->validate($data);

            if (count($errors) > 0) {
                $errors = array_map(function ($error) {
                    return [
                        'property' => $error->getPropertyPath(),
                        'message' => $error->getMessage()
                    ];
                }, iterator_to_array($errors));

                throw new BadRequestException(json_encode($errors));
            }
        }

        yield $data;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return count($argument->getAttributes(Body::class)) > 0;
    }
}