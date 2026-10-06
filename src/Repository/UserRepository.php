<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(?User $user = null) {
        $this->getEntityManager()->flush($user);
    }

    public function persist(?User $user = null) {
        $this->getEntityManager()->persist($user);
    }

    public function persistAndSave(?User $user = null) {
        $this->persist($user);
        $this->save($user);
    }
}
