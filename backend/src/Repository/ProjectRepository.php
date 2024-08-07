<?php

namespace App\Repository;

use App\Dto\CreateProjectDto;
use App\Dto\UpdateProjectDto;
use App\Entity\Project;
use App\Entity\ProjectDonationStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function fetchProjectById(int $projectId): ?Project
    {
        $data = $this->createQueryBuilder('p')
            ->where('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$data) {
            throw new NotFoundHttpException('Project not found');
        }

        return $data;
    }

    public function fetchProjects(
        array $orderBy = [],
        array $filters = [],
        int $limit = 10
    ): array
    {
        if (empty($orderBy)) {
            $orderBy = ['createdAt' => 'DESC'];
        }

        if (empty($filters)) {
            $filters = [];
        }

        if (empty($limit)) {
            $limit = 10;
        }

        $qb = $this->createQueryBuilder('p');

        foreach ($filters as $key => $value) {
            if (!Project::isValidFilterField($key)) {
                continue;
            }

            $filter_name = sprintf('filter_%s', $key);
            $filtered_input = preg_replace('/[^a-zA-Z0-9_]/', '', $value);

            if ($key === 'name') {
                $qb->andWhere('p.name LIKE :'.$filter_name)
                    ->setParameter($filter_name, '%'.$filtered_input.'%');
            } else {
                $qb->andWhere("p.$key = :$filter_name")
                    ->setParameter($filter_name, $filtered_input);
            }
        }

        foreach ($orderBy as $key => $value) {
            if (!Project::isValidSortField($key) && !in_array(strtoupper($value), ['ASC', 'DESC'])) {
                continue;
            }

            $qb->orderBy('p.' . $key, $value);
        }

        return $qb->getQuery()
            ->setMaxResults($limit)
            ->getResult();
    }

    public function isProjectOwner(int $projectId, int $userId): bool
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :projectId')
            ->andWhere('p.owner = :userId')
            ->setParameter('projectId', $projectId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    public function fetchProjectsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function deleteProject(int $id): void
    {
        $project = $this->fetchProjectById($id);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        $em = $this->getEntityManager();
        $em->remove($project);
        $em->flush();
    }

    public function createProject(CreateProjectDto $data, User $user): Project
    {
        $project = new Project();
        $project->setName($data->getName());
        $project->setDescription($data->getDescription());
        $project->setOwner($user);
        $project->setMeta($data->getMeta());
        $project->setCategory($data->getCategory());

        $em = $this->getEntityManager();
        $pdsEm = $em->getRepository(ProjectDonationStatus::class);

        $pdsEm->initializeStatus($project, false);
        $em->persist($project);
        $em->flush();

        return $project;
    }

    public function updateProject(int $id, UpdateProjectDto $data): void
    {
        $project = $this->fetchProjectById($id);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        if ($data->getName()) {
            $project->setName($data->getName());
        }

        if ($data->getDescription()) {
            $project->setDescription($data->getDescription());
        }

        if ($data->getMeta()) {
            $project->setMeta($data->getMeta());
        }

        if ($data->getCategory()) {
            $project->setCategory($data->getCategory());
        }

        $em = $this->getEntityManager();
        $em->persist($project);
        $em->flush();
    }
}
