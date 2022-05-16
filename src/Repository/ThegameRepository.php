<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Thegame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Thegame|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thegame|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thegame[]    findAll()
 * @method Thegame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThegameRepository extends ServiceEntityRepository
{
    protected $entity;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thegame::class);
        $this->entity = $this->getEntityManager();
    }
    public function count_row($keyword) {
        $list_game = $this->entity->getRepository(Game::class)->search_like($keyword);
        $query =  $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('t.cardnumber LIKE :keyword')
            ->orwhere('t.seri LIKE :keyword')
            ->orWhere('t.gia= :gia')
            ->setParameter('gia',$keyword)
            ->setParameter('keyword','%'.$keyword.'%');
        foreach ($list_game as $game) {
            $query->orWhere('t.game = '.$game->getId());
        }
        return $query->getQuery()
            ->getSingleScalarResult();
    }
    public function search($keyword,$page,$num_row) {
        $list_game = $this->entity->getRepository(Game::class)->search_like($keyword);
        $query =  $this->createQueryBuilder('t')
            ->where('t.cardnumber LIKE :keyword')
            ->orwhere('t.seri LIKE :keyword')
            ->orWhere('t.gia= :gia')
            ->setParameter('gia',$keyword)
            ->setParameter('keyword','%'.$keyword.'%');
        foreach ($list_game as $game) {
            $query->orWhere('t.game = '.$game->getId());
        }
        return $query->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult();
    }
    public function get_list($page,$num_row) {
        return $this->createQueryBuilder('t')
            ->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult();
    }
    public function add($seri, $cardnumber, $game_id,$gia)
    {
        $conn = $this->_em->getConnection();
        $conn->insert('thegame',['seri'=>$seri,'cardnumber'=>$cardnumber,'game_id'=>$game_id,'status'=>1,'gia'=>$gia]);
    }
    public function edit($id,$seri,$cardnumber,$game_id,$gia)
    {
        $conn = $this->_em->getConnection();
        $conn->update('thegame',['seri'=>$seri,'cardnumber'=>$cardnumber,'game_id'=>$game_id,'gia'=>$gia],['id'=>$id]);
    }
    public function delete($id) {
        $thegame = $this->findOneBy(['id'=>$id]);
        $this->_em->remove($thegame);
        $this->_em->flush();
    }
    public function get_list_game() {
        return $this->createQueryBuilder('t')
            ->where('t.status=1')
            ->groupBy('t.game')
            ->getQuery()->getResult();
    }
    public function get_list_gia($game_id) {
        return $this->createQueryBuilder('t')
            ->where('t.game=:id')
            ->andWhere('t.status=1')
            ->setParameter('id',$game_id)
            ->groupBy('t.gia')
            ->getQuery()->getResult();
    }
    public function get_list_by_game_and_gia($game_id,$gia) {
        return $this->createQueryBuilder('t')
            ->where('t.game=:game_id')
            ->andWhere('t.gia=:gia')
            ->andWhere('t.status=1')
            ->setParameter('game_id',$game_id)
            ->setParameter('gia',$gia)
            ->getQuery()->getResult();
    }
    public function setStatus($id,$status) {
        $thegame = $this->findOneBy(['id'=>$id]);
        $thegame->setStatus($status);
        $this->_em->flush();
    }
    // /**
    //  * @return Thegame[] Returns an array of Thegame objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Thegame
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
