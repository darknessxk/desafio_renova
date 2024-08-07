<?php

namespace App\Repository;

use App\Dto\RegisterUserDto;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserPasswordHasherInterface $passwordFn
    )
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByEmailField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function registerUser(RegisterUserDto $userDto): array
    {
        try {
            $user = new User();
            $user->setEmail($userDto->getEmail());
            $user->setFirstName($userDto->getFirstName());
            $user->setLastName($userDto->getLastName());
            $user->setPassword($this->passwordFn->hashPassword($user, $userDto->getPassword()));

            $em = $this->getEntityManager();
            $em->persist($user);
            $em->flush();

            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ];
        } catch (UniqueConstraintViolationException) {
            throw new BadRequestException('User already exists');
        } catch (\Exception) {
            throw new BadRequestException('Error creating user');
        }
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->findOneByEmailField($identifier);
    }
}
