<?php

namespace App\Repository;

use App\Entity\RegisterToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RegisterToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegisterToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegisterToken[]    findAll()
 * @method RegisterToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegisterTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegisterToken::class);
    }

    // /**
    //  * @return RegisterToken[] Returns an array of RegisterToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RegisterToken
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
