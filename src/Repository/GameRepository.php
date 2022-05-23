<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Taikhoangame;
use App\Entity\Thegame;
use App\Entity\Nhiemvu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    protected $entity;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
        $this->entity = $this->getEntityManager();
    }
    public function get_all() {
        return $this->createQueryBuilder('game')
            ->getQuery()->getResult();
    }
    // lay danh sach game có phân trang
    public function get_list($page,$num_row)
    {
        return $this->createQueryBuilder('game')
            ->getQuery()
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getResult();
    }
    public function search($keyword,$page,$num_row) {
        return $this->createQueryBuilder('game')
            ->where('game.tengame LIKE :keyword')
            ->setParameter('keyword','%'.$keyword.'%')
            ->setMaxResults($num_row)
            ->setFirstResult(($page-1)*$num_row)
            ->getQuery()->getResult();
    }
    public function count_row($keyword) {
        return  $this->createQueryBuilder('game')
            ->select('count(game.id)')
            ->where('game.tengame LIKE :keyword')
            ->setParameter('keyword','%'.$keyword.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function edit($id, $tengame, $filename) {
        $game = $this->findOneBy(array('id'=>$id));
        $game->setTengame($tengame);
        if ($filename != null) {
            $game->setAnh('/images/'.$filename);
        }
        $this->_em->flush();
    }
    public function delete($id) {
        $listTkGame = $this->entity->getRepository(Taikhoangame::class)->getAll_game($id);
        $listThegame = $this->entity->getRepository(Thegame::class)->getAll_game($id);
        $listNhiemvu = $this->entity->getRepository(Nhiemvu::class)->getAll_game($id);
        if (count($listTkGame) == 0 && count($listThegame) == 0 && count($listNhiemvu) == 0) {
            $game = $this->findOneBy(array('id'=>$id));
            $this->_em->remove($game);
            $this->_em->flush();
            return 1;
        }
        else return 0;
    }
    public function add($tengame,$filename) {
        $game = new Game();
        $game->setTengame($tengame);
        $game->setAnh('/images/'.$filename);
        $this->_em->persist($game);
        $this->_em->flush();
    }
    public function search_like($keyword) {
        return $this->createQueryBuilder('game')
            ->where('game.tengame LIKE :keyword')
            ->setParameter('keyword','%'.$keyword.'%')
            ->getQuery()->getResult();
    }
    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
