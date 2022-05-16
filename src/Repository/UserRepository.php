<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    // ham kiem tra dang nhap
    public function check_login($tendangnhap, $matkhau): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.tendangnhap= :tendangnhap')
            ->andWhere('user.matkhau= :matkhau')
            ->setParameter('tendangnhap',$tendangnhap)
            ->setParameter('matkhau',$matkhau)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function getAll($page)
    {
        return  $this->createQueryBuilder('u')
            ->getQuery()
            ->setMaxResults(10)
            ->setFirstResult(($page-1)*10)
            ->getResult();
    }
    public function count_row($keyword) {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.tendangnhap LIKE :keyword')
            ->orwhere('u.hoten LIKE :keyword')
            ->orWhere('u.email LIKE :keyword')
            ->orWhere('u.sodt LIKE :keyword')
            ->setParameter('keyword','%'.$keyword.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function getById($id): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id= :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function edit_user($id, $matkhau, $hoten, $ngaysinh, $sodt, $gioitinh, $sodu)
    {
        $conn = $this->_em->getConnection();
        return $conn->update('user',['matkhau'=>$matkhau,'hoten'=>$hoten,'ngaysinh'=>$ngaysinh, 'sodt'=>$sodt,'gioitinh'=>$gioitinh,'sodu'=>$sodu],['id'=>$id]);
    }
    public function update_user($id,$hoten,$ngaysinh,$email,$sodt,$gioitinh) {
        $user = $this->findOneBy(['id'=>$id]);
        $user->setHoten($hoten);
        $user->setEmail($email);
        $user->setSodt($sodt);
        $user->setGioitinh($gioitinh);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $user->setNgaysinh(new \DateTime($ngaysinh));
        $this->_em->flush();
    }
    public function return_status($id) {
        $user = $this->findOneBy(['id'=>$id]);
        $user->setQuyen(0);
        $this->_em->flush();
    }
    public function change_password($id,$old_password,$new_password1,$new_password2)
    {
        $user = $this->findOneBy(['id'=>$id]);
        if ($old_password != $user->getMatkhau()) return "Sai mật khẩu cũ!";
        elseif ($new_password1 != $new_password2) return "Mật khẩu mới không trùng khớp!";
        else {
            $user->setMatkhau(md5($new_password1));
            $this->_em->flush();
            return "Đổi mật khẩu thành công!";
        }
    }
    public function setPassword($id, $password) {
        $user = $this->findOneBy(['id'=>$id]);
        $user->setMatkhau($password);
        $this->_em->flush();
    }
    public function search($keyword,$page)
    {
        return $this->createQueryBuilder('u')
            ->where('u.tendangnhap LIKE :keyword')
            ->orwhere('u.hoten LIKE :keyword')
            ->orWhere('u.email LIKE :keyword')
            ->orWhere('u.sodt LIKE :keyword')
            ->setParameter('keyword','%'.$keyword.'%')
            ->getQuery()
            ->setMaxResults(10)
            ->setFirstResult(($page-1)*10)
            ->getResult();
    }
    public function lock_unlock($id) {
        $user = $this->getById($id);
//        $conn = $this->_em->getConnection();

        if ($user->getQuyen() === -1) {
            $user->setQuyen(0);
            $this->_em->flush();
        }

//             $conn->update('user',['quyen'=>0],['id'=>$id]);

        elseif ($user->getQuyen() === 0) {
            $user->setQuyen(-1);
            $this->_em->flush();
        }
//             $conn->update('user',['quyen'=>-1],['id'=>$id]);

    }
    public function delete($id) {
        $user = $this->getById($id);
        $this->_em->remove($user);
        return $this->_em->flush();
    }
    public function add($tendangnhap, $matkhau, $hoten, $ngaysinh, $email, $sodt, $gioitinh, $sodu) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $user = new User();
        $user->setTendangnhap($tendangnhap);
        $user->setMatkhau($matkhau);
        $user->setHoten($hoten);
        $user->setNgaysinh(new \DateTime($ngaysinh));
        $user->setEmail($email);
        $user->setSodt($sodt);
        $user->setGioitinh($gioitinh);
        $user->setSodu($sodu);
        $user->setQuyen(0);
        $user->setVerticalCode('');
        $this->_em->persist($user);
        $this->_em->flush();
    }
    public function check_add_user($tendangnhap, $email, $sodt) {
        $user1 = $this->createQueryBuilder('u')
            ->where('u.tendangnhap= :tendangnhap')
            ->setParameter('tendangnhap',$tendangnhap)
            ->getQuery()->getOneOrNullResult();
        if ($user1 != null) return -1;
        $user2 = $this->createQueryBuilder('u')
            ->where('u.email= :email')
            ->setParameter('email',$email)
            ->getQuery()->getOneOrNullResult();
        if ($user2 != null) return -2;
        $user3 = $this->createQueryBuilder('u')
            ->where('u.sodt= :sodt')
            ->setParameter('sodt',$sodt)
            ->getQuery()->getOneOrNullResult();
        if ($user3 != null) return -3;
        return 0;
    }
    public function check_edit_user($old_email, $new_email, $old_sodt,$new_sodt) {
        $user1 = $this->createQueryBuilder('u')
            ->where('u.email= :email')
            ->setParameter('email',$new_email)
            ->getQuery()->getOneOrNullResult();
        if ($user1 != null && $old_email!=$new_email) return -1;
        $user2 = $this->createQueryBuilder('u')
            ->where('u.sodt= :sodt')
            ->setParameter('sodt',$new_sodt)
            ->getQuery()->getOneOrNullResult();
        if ($user2 != null && $old_sodt!=$new_sodt) return -2;
        return 0;
    }
    public function search_like_name($keyword) {
        return $this->createQueryBuilder('u')
            ->where('u.hoten LIKE :keyword')
            ->setParameter('keyword','%'.$keyword.'%')
            ->getQuery()->getResult();
    }
    public function setVerticalCode($id,$code) {
        $user = $this->findOneBy(['id'=>$id]);
        $user->setVerticalCode($code);
        $this->_em->flush();
    }
    public function activeUser($id) {
        $user = $this->findOneBy(['id'=>$id]);
        $user->setQuyen(1);
        $this->_em->flush();
    }
    public function setSodu($id,$sodu) {
        $user = $this->findOneBy(['id'=>$id]);
        $user->setSodu($sodu);
        $this->_em->flush();
    }
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
