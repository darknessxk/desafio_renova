<?php

namespace App\Repository;

use App\Entity\Donation;
use App\Entity\Project;
use App\Entity\ProjectDonationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectDonationStatus>
 */
class ProjectDonationStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectDonationStatus::class);
    }

    public function fetchStatusByProjectId(int $projectId): array
    {
        return $this->createQueryBuilder('pds')
            ->where('pds.project = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getResult();
    }

    public function initializeStatus(Project $project, bool $flush = true): void
    {
        $status = new ProjectDonationStatus();
        $status->setDonationCount(0);
        $status->setDonationTotal(0.0);
        $status->setProject($project);

        $em = $this->getEntityManager();
        $em->persist($status);

        if ($flush) {
            $em->flush();
        }
    }

    public function createOrUpdateStatus(Project $project, Donation $donation, bool $flush = true): void
    {
        $status = $this->findOneBy(['project' => $project->getId()]);
        $em = $this->getEntityManager();

        if ($status === null) {
            $status = new ProjectDonationStatus();
            $status->setDonationCount(1);
            $status->setDonationTotal($donation->getValue());
            $status->setProject($project);
        } else {
            $status->setDonationCount($status->getDonationCount() + 1);
            $status->setDonationTotal($status->getDonationTotal() + $donation->getValue());
        }

        $em->persist($status);

        if ($flush) {
            $em->flush();
        }
    }
}
