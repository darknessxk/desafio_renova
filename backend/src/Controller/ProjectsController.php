<?php

declare(strict_types=1);

namespace App\Controller;

use App\ArgumentResolver\Body;
use App\ArgumentResolver\QueryParam;
use App\Config\Categories;
use App\Dto\CreateDonationDto;
use App\Dto\CreateProjectDto;
use App\Dto\UpdateProjectDto;
use App\Entity\User;
use App\Repository\DonationRepository;
use App\Repository\ProjectDonationStatusRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectsController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    )
    {
    }

    #[Route('/api/projects/categories', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        return new JsonResponse(
            Categories::cases()
        );
    }

    #[Route('/api/projects', methods: ['GET'])]
    public function fetchList(
        #[QueryParam('orderBy', false)] array $orderBy,
        #[QueryParam('filters', false)] array $filters,
        #[QueryParam('limit', false)] int $limit,
        ProjectRepository $projectRepository
    ): JsonResponse
    {
        $data = $projectRepository->fetchProjects($orderBy, $filters, $limit);

        if (count($data) === 0) {
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => [
                    'project:read', 'project:owner', 'project:status'
                ]
            ]),
            json: true
        );
    }

    #[Route('/api/projects/{id}', methods: ['GET'])]
    public function getById(
        #[QueryParam('id', true)] int $id,
        ProjectRepository $projectRepository
    ): JsonResponse
    {
        $data = $projectRepository->fetchProjectById($id);

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => [
                    'project:read', 'project:owner', 'project:status'
                ]
            ]),
            json: true
        );
    }

    #[Route('/api/projects/{id}', methods: ['POST'])]
    public function updateProject(
        #[CurrentUser] ?User $user,
        #[QueryParam('id', true)] int $id,
        #[Body] UpdateProjectDto $data,
        ProjectRepository $projectRepository
    ): JsonResponse | AccessDeniedException
    {
        if (!$user) {
            return $this->createAccessDeniedException();
        }

        if (!$projectRepository->isProjectOwner($id, $user->getId())) {
            return $this->createAccessDeniedException();
        }

        $projectRepository->updateProject($id, $data);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/projects', methods: ['PUT'])]
    public function createProject(
        #[CurrentUser] ?User $user,
        #[Body] CreateProjectDto $data,
        ProjectRepository $projectRepository
    ): JsonResponse | AccessDeniedException
    {
        if (!$user) {
            return $this->createAccessDeniedException();
        }

        $data = $projectRepository->createProject($data, $user);

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => [
                    'project:read', 'project:owner', 'project:status'
                ]
            ]),
            json: true
        );
    }

    #[Route('/api/projects/{id}', methods: ['DELETE'])]
    public function deleteProject(
        #[CurrentUser] ?User $user,
        #[QueryParam('id', true)] int $id,
        ProjectRepository $projectRepository
    ): JsonResponse | AccessDeniedException
    {
        if (!$user) {
            return $this->createAccessDeniedException();
        }

        if (!$projectRepository->isProjectOwner($id, $user->getId())) {
            return $this->createAccessDeniedException();
        }

        $projectRepository->deleteProject($id);
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/projects/{id}/donations/limit', methods: ['GET'])]
    public function getDonationsStatusByProjectId(
        #[QueryParam('id', true)] int $id,
        ProjectDonationStatusRepository $pdsRepository
    ): JsonResponse
    {
        $data = $pdsRepository->fetchStatusByProjectId($id);

        if (count($data) === 0) {
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => [
                    'project:donation:status'
                ]
            ]),
            json: true
        );
    }

    #[Route('/api/projects/{id}/donations', methods: ['PUT'])]
    public function registerDonationByProjectId(
        #[QueryParam('id', true)] int $id,
        #[Body] CreateDonationDto $data,
        DonationRepository $donationRepository
    ): JsonResponse
    {
        $data = $donationRepository->createDonation($id, $data);

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => [
                    'donation:read', 'project:read'
                ]
            ]),
            Response::HTTP_CREATED, json: true);
    }

    #[Route('/api/projects/{id}/donations', methods: ['GET'])]
    public function getDonationsByProjectId(
        #[QueryParam('id', true)] int $id,
        DonationRepository $donationRepository
    ): JsonResponse
    {
        $data = $donationRepository->fetchDonationsByProjectId($id);

        if (count($data) === 0) {
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => [
                    'donation:read', 'project:read'
                ]
            ]),
            json: true
        );
    }
}
