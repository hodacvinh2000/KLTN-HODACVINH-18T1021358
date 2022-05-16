<?php

namespace App\Repository;

use App\Entity\Anh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Anh|null find($id, $lockMode = null, $lockVersion = null)
 * @method Anh|null findOneBy(array $criteria, array $orderBy = null)
 * @method Anh[]    findAll()
 * @method Anh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anh::class);
    }
    public function add($filename, $tkgame_id) {
        $link = "/images/".$filename;
        $conn = $this->_em->getConnection();
        $conn->insert('anh',['link'=>$link,'tkgame_id'=>$tkgame_id]);
    }
    public function delete_all_image($tkgame_id) {
        $list_image = $this->findBy(['tkgame'=>$tkgame_id]);
        foreach ($list_image as $image) $this->_em->remove($image);
        $this->_em->flush();
    }
    public function delete($id) {
        $image = $this->findOneBy(['id'=>$id]);
        $this->_em->remove($image);
        $this->_em->flush();
    }
    public function get_list_account($id) {
        return $this->createQueryBuilder('anh')
            ->where('anh.tkgame=:id')
            ->setParameter('id',$id)
            ->getQuery()->getResult();
    }
    // /**
    //  * @return Anh[] Returns an array of Anh objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Anh
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
