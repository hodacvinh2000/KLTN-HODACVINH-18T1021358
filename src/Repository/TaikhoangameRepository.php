<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Taikhoangame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taikhoangame|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taikhoangame|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taikhoangame[]    findAll()
 * @method Taikhoangame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaikhoangameRepository extends ServiceEntityRepository
{
    protected $entity;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taikhoangame::class);
        $this->entity = $this->getEntityManager();
    }
    public function getAll_game($id) {
        return $this->createQueryBuilder('tkgame')
        ->where('tkgame.game=:id')
        ->setParameter('id',$id)->getQuery()->getResult();
    }
    public function add($game_id,$username,$ingame,$password,$description,$gia) {
        $conn = $this->_em->getConnection();
        $account = $this->findOneBy(['username'=>$username,'game'=>$game_id]);
        if($account) {
            return -1;
        }
        else {
            $conn->insert('taikhoangame',['game_id'=>$game_id,'username'=>$username,'ingame'=>$ingame,'password'=>$password,'description'=>$description,'gia'=>$gia,'status'=>1]);
            return 1;
        }
    }
    public function edit($id,$game_id,$old_username,$username,$password,$description,$gia) {
        $conn = $this->_em->getConnection();
        $account = $this->findOneBy(['username'=>$username,'game'=>$game_id]);
        if($account && $username != $old_username) {
            return -1;
        }
        else {
            $conn->update('taikhoangame',['game_id'=>$game_id,'username'=>$username,'password'=>$password,'description'=>$description,'gia'=>$gia],['id'=>$id]);
            return 1;
        }
    }
    public function count_row($keyword) {
        $list_game = $this->entity->getRepository(Game::class)->search_like($keyword);
        $query =  $this->createQueryBuilder('tkgame')
            ->select('count(tkgame.id)')
            ->where('tkgame.username LIKE :keyword')
            ->orWhere('tkgame.gia= :gia')
            ->orWhere('tkgame.ingame LIKE :keyword');
        foreach ($list_game as $game) {
            $query->orWhere('tkgame.game = '.$game->getId());
        }
        $query->setParameter('keyword','%'.$keyword.'%')
            ->setParameter('gia',$keyword);
        return $query->getQuery()
            ->getSingleScalarResult();
    }
    public function count_row_game($game_id,$keyword) {
        $query =  $this->createQueryBuilder('tkgame')
            ->select('count(tkgame.id)')
            ->orWhere('tkgame.gia=:gia')
            ->orWhere('tkgame.ingame LIKE :keyword')
            ->setParameter('gia',$keyword)
            ->setParameter('keyword','%'.$keyword.'%')
            ->andWhere('tkgame.status=1')
            ->andWhere('tkgame.game=:game_id')->setParameter('game_id',$game_id);
        return $query->getQuery()
            ->getSingleScalarResult();
    }
    public function search($keyword,$page,$num_row) {
        $list_game = $this->entity->getRepository(Game::class)->search_like($keyword);
        $query =  $this->createQueryBuilder('tkgame')
            ->where('tkgame.username LIKE :keyword')
            ->orWhere('tkgame.gia= :gia')
            ->orWhere('tkgame.ingame LIKE :keyword');
        foreach ($list_game as $game) {
            $query->orWhere('tkgame.game = '.$game->getId());
        }
        $query->setParameter('keyword','%'.$keyword.'%')
            ->setParameter('gia',$keyword);
        return $query->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult();
    }
    public function get_list($page,$num_row)
    {
        return $this->createQueryBuilder('tkgame')
            ->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult()
            ;
    }
    public function get_list_game($game_id,$page,$num_row) {
        return $this->createQueryBuilder('tkgame')
            ->where('tkgame.status=1')
            ->andWhere('tkgame.game=:game_id')->setParameter('game_id',$game_id)
            ->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult()
            ;
    }
    public function search_game($game_id,$keyword,$page,$num_row) {
        return $this->createQueryBuilder('tkgame')
            ->orWhere('tkgame.gia=:gia')
            ->orWhere('tkgame.ingame LIKE :keyword')
            ->setParameter('gia',$keyword)
            ->setParameter('keyword','%'.$keyword.'%')
            ->andWhere('tkgame.status=1')
            ->andWhere('tkgame.game=:game_id')->setParameter('game_id',$game_id)
            ->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult()
            ;
    }
    public function delete($id) {
        $tkgame = $this->findOneBy(['id'=>$id]);
        $this->_em->remove($tkgame);
        $this->_em->flush();
    }
    public function setStatus($id,$status) {
        $tkgame = $this->findOneBy(['id'=>$id]);
        $tkgame->setStatus($status);
        $this->_em->flush();
    }

    // /**
    //  * @return Taikhoangame[] Returns an array of Taikhoangame objects
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
    public function findOneBySomeField($value): ?Taikhoangame
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
