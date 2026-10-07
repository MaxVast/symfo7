<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\User;
use App\Enum\RegistrationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    public function save(?Registration $registration = null) {
        $this->getEntityManager()->flush($registration);
    }

    public function persist(?Registration $registration = null) {
        $this->getEntityManager()->persist($registration);
    }

    public function persistAndSave(?Registration $registration = null) {
        $this->persist($registration);
        $this->save($registration);
    }

    public function countConfirmed(Event $e): int
    {
        return (int)$this->createQueryBuilder('r')->select('COUNT(r.id)')->andWhere('r.event=:event')->andWhere('r.status=:status')->setParameter('event', $e)->setParameter('status', RegistrationStatus::Confirmed)->getQuery()->getSingleScalarResult();
    }

    public function findWaitlistFirst(Event $e): ?Registration
    {
        return $this->createQueryBuilder('r')->andWhere('r.event=:event')->andWhere('r.status=:status')->setParameter('event', $e)->setParameter('status', RegistrationStatus::Waitlist)->orderBy('r.createdAt', 'ASC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    public function findActive(Event $e, User $u): ?Registration
    {
        return $this->createQueryBuilder('r')->andWhere('r.event=:event')->andWhere('r.user=:user')->andWhere('r.status != :cancelled')->setParameter('event', $e)->setParameter('user', $u)->setParameter('cancelled', RegistrationStatus::Cancelled)->getQuery()->getOneOrNullResult();
    }

    public function findForUser(User $u): array
    {
        return $this->findBy(['user' => $u], ['createdAt' => 'DESC']);
    }
}
