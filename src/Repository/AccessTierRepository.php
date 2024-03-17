<?php

namespace App\Repository;

use App\Entity\AccessTier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessTier>
 *
 * @method AccessTier|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessTier|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessTier[]    findAll()
 * @method AccessTier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessTierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessTier::class);
    }

    //    /**
    //     * @return AccessTier[] Returns an array of AccessTier objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AccessTier
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
