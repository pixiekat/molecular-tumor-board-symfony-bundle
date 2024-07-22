<?php

namespace Pixiekat\MolecularTumorBoard\Repository;

use App\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tumor>
 *
 * @method Tumor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tumor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tumor[]    findAll()
 * @method Tumor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TumorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tumor::class);
    }

//    /**
//     * @return Tumor[] Returns an array of Tumor objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tumor
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
