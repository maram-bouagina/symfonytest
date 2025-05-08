<?php
namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findByPriceRange(?float $minValue, ?float $maxValue): array
    {
        $qb = $this->createQueryBuilder('a');

        if ($minValue !== null) {
            $qb->andWhere('a.prix >= :minVal')
               ->setParameter('minVal', $minValue);
        }

        if ($maxValue !== null) {
            $qb->andWhere('a.prix <= :maxVal')
               ->setParameter('maxVal', $maxValue);
        }

        return $qb->orderBy('a.id', 'ASC')
                 ->setMaxResults(10)
                 ->getQuery()
                 ->getResult();
    }
}