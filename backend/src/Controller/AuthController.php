<?php

namespace App\Controller;

use App\ArgumentResolver\Body;
use App\Dto\IsInUseDto;
use App\Dto\RegisterUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthController extends AbstractController
{
    #[Route('/api/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ]);
    }


    #[Route('/api/auth/is_in_use', methods: ['POST'])]
    public function isInUse(
        #[Body] IsInUseDto $body,
        UserRepository $userRepository
    ): JsonResponse
    {
        if ($userRepository->findOneByEmailField($body->getEmail()) !== null) {
            return $this->json(['message' => 'Email is already in use'], Response::HTTP_CONFLICT);
        } else {
            return $this->json(['message' => 'Email is not in use']);
        }
    }

    #[Route('/api/auth/register', methods: ['POST'])]
    public function register(
        #[Body] RegisterUserDto $body,
        UserRepository $userRepository
    ): JsonResponse
    {
        return $this->json($userRepository->registerUser($body));
    }
}
