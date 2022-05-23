<?php

namespace App\Repository;

use App\Entity\Binhluannhiemvu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Null_;

/**
 * @method Binhluannhiemvu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Binhluannhiemvu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Binhluannhiemvu[]    findAll()
 * @method Binhluannhiemvu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BinhluannhiemvuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Binhluannhiemvu::class);
    }
    public function delete_allcomment($id) {
        $listComment = $this->createQueryBuilder('bl')
        ->where('bl.nhiemvu=:id')
        ->setParameter('id',$id)
        ->getQuery()->getResult();
        foreach ($listComment as $comment) {
            $this->delete_allFeedback($comment->getId());
            $this->_em->remove($comment);
        }
        $this->_em->flush();
    }
    public function delete_allFeedback($id) {
        $listFeedback = $this->createQueryBuilder('bl')
        ->where('bl.phanhoi=:id')
        ->setParameter('id',$id)->getQuery()->getResult();
        foreach ($listFeedback as $feedback) {
            if ($feedback->getPhanhoi() != null) {
                $this->delete_allFeedback($feedback->getId());
            }
            $this->_em->remove($feedback);
        }
        $this->_em->flush();
    }
    public function getAll_id($id) {
        return $this->createQueryBuilder('bl')
            ->where('bl.nguoibinhluan=:id')
            ->setParameter('id',$id)
            ->getQuery()->getResult();
    }
    public function danhsach_binhluan($id)
    {
        return $this->createQueryBuilder('bl')
            ->where('bl.phanhoi is NULL')
            ->andWhere('bl.nhiemvu= :nhiemvuid')
            ->setParameter('nhiemvuid',$id)
            ->getQuery()
            ->getResult();
    }
    public function danhsach_phanhoi($id)
    {
        return $this->createQueryBuilder('bl')
            ->andWhere('bl.phanhoi= :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }
    public function thembinhluan($nhiemvu_id, $nguoibinhluan_id, $binhluan): ?int
    {
        $thoigian = date('Y-m-d H:i:s');
        $conn = $this->_em->getConnection();
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        return $conn->insert('binhluannhiemvu', ['nhiemvu_id' => $nhiemvu_id, 'nguoibinhluan_id' => $nguoibinhluan_id, 'binhluan' => $binhluan, 'thoigian' => $thoigian,'sophanhoi'=>0]);
    }
    public function get_new_binhluan($nhiemvu_id,$nguoibinhluan_id) {
        return $this->createQueryBuilder('bl')
            ->orderBy('bl.id','DESC')
            ->where('bl.nhiemvu= :id')
            ->andWhere('bl.nguoibinhluan= :id_nguoibinhluan')
            ->setParameter('id',$nhiemvu_id)
            ->setParameter('id_nguoibinhluan',$nguoibinhluan_id)
            ->getQuery()->setMaxResults(1)->getResult();
    }
    public function xoabinhluan($id)
    {
        $binhluan = $this->findOneBy(['id'=>$id]);
        if ($binhluan->getSophanhoi() > 0) {
            $list_phanhoi = $this->createQueryBuilder('phanhoi')
                ->where('phanhoi.phanhoi=:id')
                ->setParameter('id',$id)
                ->getQuery()->getResult();
            foreach ($list_phanhoi as $phanhoi) {
                $this->xoabinhluan($phanhoi->getId());
            }
        }
        if ($binhluan->getPhanhoi() != null) {
            $binhluan_answered = $this->findOneBy(['id'=>$binhluan->getPhanhoi()->getId()]);
            $binhluan_answered->setSophanhoi($binhluan_answered->getSophanhoi()-1);
        }
        $this->_em->remove($binhluan);
        $this->_em->flush();
    }
    public function traloibinhluan($id,$id_nguoibinhluan,$content) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $thoigian = date('Y-m-d H:i:s');
        $binhluan = $this->findOneBy(['id'=>$id]);
        $sophanhoi = $binhluan->getSophanhoi() + 1;
        $binhluan->setSophanhoi($sophanhoi);
        $this->_em->flush();
        $conn = $this->_em->getConnection();
        return $conn->insert('binhluannhiemvu',['nhiemvu_id'=>$binhluan->getNhiemvu()->getId(),'nguoibinhluan_id'=>$id_nguoibinhluan,'binhluan'=>$content,'thoigian'=>$thoigian,'phanhoi_id'=>$id,'sophanhoi'=>0]);
    }
    public function get_new_phanhoi($id,$id_nguoibinhluan) {
        return $this->createQueryBuilder('bl')
            ->orderBy('bl.id','DESC')
            ->where('bl.phanhoi= :id')
            ->andWhere('bl.nguoibinhluan= :id_nguoibinhluan')
            ->setParameter('id',$id)
            ->setParameter('id_nguoibinhluan',$id_nguoibinhluan)
            ->getQuery()->setMaxResults(1)->getResult();
    }
    // /**
    //  * @return Binhluannhiemvu[] Returns an array of Binhluannhiemvu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Binhluannhiemvu
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
