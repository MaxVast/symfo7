<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(?Category $category = null) {
        $this->getEntityManager()->flush($category);
    }

    public function persist(?Category $category = null) {
        $this->getEntityManager()->persist($category);
    }

    public function persistAndSave(?Category $category = null) {
        $this->persist($category);
        $this->save($category);
    }
}
