<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JwtCreatedListener
{
    public function __construct(private readonly RequestStack $requestStack, private readonly UserRepository $userRepository)
    {
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getData();
        $user = $this->userRepository->findOneByEmailField($payload['username']);

        if (!$user) {
            return;
        }

        $payload['id'] = $user->getId();
        $payload['ip'] = $request->getClientIp();

        $expiration = new \DateTime('+1 day');
        $expiration->setTime(8, 0);

        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);
    }
}