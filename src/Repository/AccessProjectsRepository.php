<?php

namespace App\Repository;

use App\Entity\AccessProjects;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessProjects>
 *
 * @method AccessProjects|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessProjects|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessProjects[]    findAll()
 * @method AccessProjects[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessProjectsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessProjects::class);
    }

//    /**
//     * @return AccessProjects[] Returns an array of AccessProjects objects
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

//    public function findOneBySomeField($value): ?AccessProjects
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
