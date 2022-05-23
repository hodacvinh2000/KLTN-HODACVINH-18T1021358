<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Nhiemvu;
use App\Entity\User;
use App\Entity\Binhluannhiemvu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method Nhiemvu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nhiemvu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nhiemvu[]    findAll()
 * @method Nhiemvu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NhiemvuRepository extends ServiceEntityRepository
{
    protected $entity;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nhiemvu::class);
        $this->entity = $this->getEntityManager();
    }
    public function getAll_game($id) {
        return $this->createQueryBuilder('nv')
        ->where('nv.game=:id')
        ->setParameter('id',$id)->getQuery()->getResult();
    }
    public function getAll_id($id) {
        return $this->createQueryBuilder('nv')
            ->where('nv.user=:id')
            ->setParameter('id',$id)
            ->getQuery()->getResult();
    }
    // lay danh sach tat ca nhiem vu
    public function get_list($page,$num_row)
    {
        return $this->createQueryBuilder('nhiemvu')
            ->addOrderBy('nhiemvu.id','DESC')
            ->addOrderBy('nhiemvu.ngaydang', 'DESC')
            ->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult()
            ;
    }
    // lay danh sach nhiem vu co trang thai la 1
    public function get_list_active($page,$num_row)
    {
        return $this->createQueryBuilder('nhiemvu')
            ->where('nhiemvu.trangthai = 1')
            ->addOrderBy('nhiemvu.id','DESC')
            ->addOrderBy('nhiemvu.ngaydang', 'DESC')
            ->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult()
            ;
    }
    public function get_list_for_user($id,$page,$num_row) {
        return $this->createQueryBuilder('nv')
            ->orderBy('nv.ngaydang','DESC')
            ->where('nv.user=:id')
            ->setParameter('id',$id)
            ->getQuery()->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)->getResult();
    }
    public function get_list_for_game($id) {
        $find_game = $this->entity->getRepository(Game::class)->findOneBy(['id' => $id]);
        $find_nhiemvu = $this->findOneBy(['game' => $find_game]);
        return $find_nhiemvu;
    }

    public function count_row($keyword) {
        $list_user = $this->entity->getRepository(User::class)->search_like_name($keyword);
        $list_game = $this->entity->getRepository(Game::class)->search_like($keyword);
        $query =  $this->createQueryBuilder('nv')
            ->select('count(nv.id)')
            ->where('nv.tieude LIKE :keyword')
            ->orwhere('nv.noidung LIKE :keyword');
        foreach ($list_user as $user) {
            $query->orWhere('nv.user = '.$user->getId());
        }
        foreach ($list_game as $game) {
            $query->orWhere('nv.game = '.$game->getId());
        }
        $query->setParameter('keyword','%'.$keyword.'%');
        return $query->getQuery()
            ->getSingleScalarResult();
    }
    public function count_row_for_user($keyword,$id) {
        return $this->createQueryBuilder('nv')
            ->select('count(nv.id)')
            ->orwhere('nv.tieude LIKE :keyword')
            ->orWhere('nv.noidung LIKE :keyword')
            ->andWhere('nv.user=:id')
            ->setParameter('keyword','%'.$keyword.'%')
            ->setParameter('id',$id)
            ->getQuery()->getSingleScalarResult();
    }
    public function search($keyword,$page,$num_row) {
        $list_user = $this->entity->getRepository(User::class)->search_like_name($keyword);
        $list_game = $this->entity->getRepository(Game::class)->search_like($keyword);
        $query =  $this->createQueryBuilder('nv')
            ->where('nv.tieude LIKE :keyword')
            ->orwhere('nv.noidung LIKE :keyword');
        foreach ($list_user as $user) {
            $query->orWhere('nv.user = '.$user->getId());
        }
        foreach ($list_game as $game) {
            $query->orWhere('nv.game = '.$game->getId());
        }
        $query->setParameter('keyword','%'.$keyword.'%');
        return $query->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult();
    }
    public function search_for_user($keyword,$page,$num_row,$user_id) {
        $query =  $this->createQueryBuilder('nv')
            ->orwhere('nv.tieude LIKE :keyword')
            ->orWhere('nv.noidung LIKE :keyword')
            ->andWhere('nv.user=:id')
            ->setParameter('keyword','%'.$keyword.'%')
            ->setParameter('id',$user_id);
        return $query->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult();
    }
    public function duyet_nhiemvu($id,$status) {
        $nhiemvu = $this->findOneBy(['id'=>$id]);
        $nhiemvu->setTrangthai($status);
        $this->_em->flush();
    }
    public function delete_nhiemvu($id) {
        $this->entity->getRepository(Binhluannhiemvu::class)->delete_allcomment($id);
        $nhiemvu = $this->findOneBy(['id'=>$id]);
        $this->_em->remove($nhiemvu);
        $this->_em->flush();
    }
    public function edit_nhiemvu($id,$tieude,$noidung,$trangthai,$trangthaicu,$game_id) {
        $conn = $this->_em->getConnection();
        if ($trangthai != $trangthaicu && $trangthai == '1')
            $this->update_ngaydang($id);
        return $conn->update('nhiemvu',['tieude'=>$tieude,'noidung'=>$noidung,'trangthai'=>$trangthai,'game_id'=>$game_id],['id'=>$id]);
    }
    public function update_ngaydang($id) {
        $nhiemvu = $this->findOneBy(['id'=>$id]);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $date = new \DateTime();
        $nhiemvu->setNgaydang($date);
        $this->_em->flush();
    }
    public function add_nhiemvu($user_id, $tieude, $game_id, $noidung) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $thoigian = date('Y-m-d H:i:s');
        $conn = $this->_em->getConnection();
        return $conn->insert('nhiemvu',['user_id'=>$user_id,'tieude'=>$tieude,'game_id'=>$game_id,'noidung'=>$noidung,'ngaydang'=>$thoigian,'trangthai'=>0]);
    }
    public function update_nhiemvu_user($id,$tieude,$game_id,$noidung) {
        $conn = $this->_em->getConnection();
        return $conn->update('nhiemvu',['tieude'=>$tieude,'game_id'=>$game_id,'noidung'=>$noidung],['id'=>$id]);
    }
    public function cancel_mission($id) {
        $nhiemvu = $this->findOneBy(['id'=>$id]);
        $nhiemvu->setTrangthai(-1);
        $this->_em->flush();
    }
    public function complete_mission($id) {
        $nhiemvu = $this->findOneBy(['id'=>$id]);
        $nhiemvu->setTrangthai(-3);
        $this->_em->flush();
    }
    // /**
    //  * @return Nhiemvu[] Returns an array of Nhiemvu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Nhiemvu
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
