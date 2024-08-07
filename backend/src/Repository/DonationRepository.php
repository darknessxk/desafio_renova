<?php

namespace App\Repository;

use App\Dto\CreateDonationDto;
use App\Entity\Donation;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Donation>
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly ProjectDonationStatusRepository $pds,
    )
    {
        parent::__construct($registry, Donation::class);
    }

    public function fetchDonationsByProjectId(int $projectId): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.project = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getResult();
    }

    public function createDonation(
        int $id,
        CreateDonationDto $data
    ): Donation
    {
        $em = $this->getEntityManager();

        $projectRepository = $em->getRepository(Project::class);
        $project = $projectRepository->fetchProjectById($id);

        if ($project === null) {
            throw new NotFoundHttpException('Project not found');
        }

        if ($project->getMeta() <= $project->getProjectDonationStatus()->getDonationTotal()) {
            throw new BadRequestHttpException('Project is already fully funded');
        }

        if ($project->getMeta() < $project->getProjectDonationStatus()->getDonationTotal() + $data->getValue()) {
            throw new BadRequestHttpException('Donation exceeds project meta');
        }

        $donation = new Donation();
        $donation->setOrigin($data->getOrigin());
        $donation->setValue($data->getValue());
        $donation->setProject($project);

        $em->persist($donation);
        $em->flush();

        $this->pds->createOrUpdateStatus($project, $donation);

        return $donation;
    }
}
