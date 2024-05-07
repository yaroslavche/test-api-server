<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<User> */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /** @return User[] */
    public function findByGroupIdentifier(int $groupIdentifier): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.userGroups', 'g', 'WITH', 'g.id = :groupIdentifier')
            ->setParameter('groupIdentifier', $groupIdentifier)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
