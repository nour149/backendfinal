<?php

namespace App\Repository;

use App\Entity\AccessProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessProject>
 *
 * @method AccessProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessProject[]    findAll()
 * @method AccessProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessProject::class);
    }

    //    /**
    //     * @return AccessProject[] Returns an array of AccessProject objects
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

    //    public function findOneBySomeField($value): ?AccessProject
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
