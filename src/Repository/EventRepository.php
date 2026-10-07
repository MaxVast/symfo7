<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Category;
use App\Entity\User;
use App\Enum\EventStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

final class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(?Event $event = null) {
        $this->getEntityManager()->flush($event);
    }

    public function persist(?Event $event = null) {
        $this->getEntityManager()->persist($event);
    }

    public function persistAndSave(?Event $event = null) {
        $this->persist($event);
        $this->save($event);
    }

    public function findEventsPublished(): array
    {
        $query = $this->createQueryBuilder('e')
            ->andWhere('e.status=:status')
            ->setParameter('status', EventStatus::Published);

        return $query->getQuery()->getResult();
    }

    public function findPage(?Category $category = null, ?string $search = null, int $page = 1, int $limit = 9): array
    {
        $q = $this->findUpcoming($category, $search)->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);
        return $q->getQuery()->getResult();
    }

    public function findUpcoming(?Category $category = null, ?string $search = null): QueryBuilder
    {
        $q = $this->createQueryBuilder('e')->leftJoin('e.category', 'c')->addSelect('c')->andWhere('e.status=:status')->andWhere('e.startAt>:now')->setParameter('status', EventStatus::Published)->setParameter('now', new \DateTimeImmutable())->orderBy('e.startAt', 'ASC');
        if ($category) $q->andWhere('e.category=:category')->setParameter('category', $category);
        if ($search) $q->andWhere('LOWER(e.title) LIKE :q OR LOWER(e.description) LIKE :q')->setParameter('q', '%' . mb_strtolower($search) . '%');
        return $q;
    }

    public function countUpcoming(?Category $category = null, ?string $search = null): int
    {
        return (int)$this->findUpcoming($category, $search)->select('COUNT(e.id)')->resetDQLPart('orderBy')->getQuery()->getSingleScalarResult();
    }

    public function findByOrganizer(User $u): array
    {
        return $this->findBy(['organizer' => $u], ['startAt' => 'ASC']);
    }

    public function findPublishedFeatured(int $limit = 6): array
    {
        return $this->findUpcoming()->setMaxResults($limit)->getQuery()->getResult();
    }
}
