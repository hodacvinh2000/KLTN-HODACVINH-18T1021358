<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Thegame;
use App\Repository\AdminRepository;
use App\Repository\AnhRepository;
use App\Repository\GameRepository;
use App\Repository\NhiemvuRepository;
use App\Repository\TaikhoangameRepository;
use App\Repository\ThegameRepository;
use App\Service\FileUploader;
use phpDocumentor\Reflection\Types\True_;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    private $requestStack;
    private $current_admin;
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->session = $requestStack->getSession();
        $this->current_admin = $this->session->get('admin');
    }
    /**
     * @Route("/", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/login", name="admin_login", methods={"GET","POST"})
     * @param Request $request
     * @param Session $session
     * @param AdminRepository $adminRepository
     * @return Response
     */
    public function admin_login(Request $request, Session $session, AdminRepository $adminRepository) {
        if ($this->current_admin == null) {
            if ($request->isMethod("POST")) {
                $username = $request->get("username");
                $password = $request->get("password");
                $admin = $adminRepository->check_login($username,$password);
                if ($admin != null) {
                    $session->set('admin',$admin);
                    return $this->redirectToRoute('quanly_user');
                }
                else {
                    return $this->render('admin/login.html.twig',['message'=>'Đăng nhập sai!']);
                }
            }
            return $this->render('admin/login.html.twig',['message'=>null]);
        }
        else {
            return $this->redirectToRoute('quanly_user');
        }
    }

    /**
     * @Route("/logout", name="admin_logout")
     * @param Session $session
     * @return RedirectResponse
     */
    public function admin_logout(Session $session) {
        $session->remove('admin');
        return $this->redirectToRoute('admin_login');
    }

    /**
     * @Route("/quanly_user", name="quanly_user")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Session $session
     * @return JsonResponse|Response
     */
    public function show_user(Request $request, UserRepository $userRepository, Session $session): Response
    {
        if ($this->current_admin == null) return $this->redirectToRoute('admin_login');
        $count_row = $userRepository->count_row("");
        $num_page = 0;
        if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
        else $num_page = (int)($count_row/10);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != null) {
                $count_row = $userRepository->count_row($keyword);
                $list_user = $userRepository->search($keyword,$page);
            }
            else {
                $list_user = $userRepository->getAll($page);
            }
            if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
            else $num_page = (int)($count_row/10);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_user as $user) {
                $temp = array(
                    'id' => $user->getId(),
                    'tendangnhap' => $user->getTendangnhap(),
                    'hoten' => $user->getHoten(),
                    'ngaysinh' => $user->getNgaysinh(),
                    'email' => $user->getEmail(),
                    'sodt' => $user->getSodt(),
                    'gioitinh' => $user->getGioitinh(),
                    'sodu' => $user->getSodu(),
                    'quyen' => $user->getQuyen(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_user = $userRepository->getAll(1);
            $message = $session->get('message');
            $session->remove('message');
            return $this->render('admin/user.html.twig', [
                'list_user' => $list_user,'num_page'=>$num_page,'page'=>1,'message'=>$message,
            ]);
        }
    }

    /**
     * @Route("/quanly_user/edit/{id}", name="edit_user_admin", methods={"GET","POST"})
     * @param UserRepository $userRepository
     * @param $id
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    public function edit_user_admin(UserRepository $userRepository, $id, Request $request, Session $session)
    {
        if ($this->current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $message = "";
            $id = $request->get('id');
            $hoten = $request->get('hoten');
            $old_email = $request->get('old_email');
            $old_sodt = $request->get('old_sodt');
            $sodt = $request->get('sodt');

            // Check sodt
            $pattern = "/^[0-9]+$/";
            if(!preg_match($pattern, $sodt) || strlen($sodt) > 13) $message = "Số điện thoại không hợp lệ";
            $ngaysinh = $request->get('ngaysinh');
            $gioitinh = $request->get('gioitinh');
            $matkhau = $request->get('matkhau');
            $sodu = $request->get('sodu');
            $status = $userRepository->check_edit_user($old_email, $old_email, $old_sodt,$sodt);
            if ($status == -2) $message = "Trùng số điện thoại";
            if ($message == ""){
                $this->session->set('message','Cập nhật user thành công!');
                $userRepository->edit_user($id,md5($matkhau),$hoten,$ngaysinh,$sodt,$gioitinh,$sodu);
                return $this->redirectToRoute('quanly_user');
            }
            $user = $userRepository->getById($id);
            return $this->render('admin/form_user.html.twig', ['user'=>$user,'message'=>$message]);
        }
        else {
            $user = $userRepository->getById($id);
            return $this->render('admin/form_user.html.twig', ['user'=>$user,'message'=>""]);
        }
    }

    /**
     * @Route("/quanly_user/lock_unlock/{id}", name="lock_unlock_user", methods={"GET","POST"})
     * @param $id
     * @param UserRepository $userRepository
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse
     */
    public function lock_unlock($id, UserRepository $userRepository, Request $request, Session $session) {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isXmlHttpRequest()) {


            $userRepository->lock_unlock($id);

            $user = $userRepository->getById($id);
            $jsonData = array();
            $idx = 0;

            if ($user) {
                $temp = array(
                    'id' => $user->getId(),
                    'tendangnhap' => $user->getTendangnhap(),
                    'hoten' => $user->getHoten(),
                    'ngaysinh' => $user->getNgaysinh(),
                    'email' => $user->getEmail(),
                    'sodt' => $user->getSodt(),
                    'gioitinh' => $user->getGioitinh(),
                    'sodu' => $user->getSodu(),
                    'quyen' => $user->getQuyen(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_user/delete/{id}", name="delete_user", methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param $id
     * @param Session $session
     * @return JsonResponse|RedirectResponse
     */
    public function delete_user(Request $request, UserRepository $userRepository, $id, Session $session) {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $userRepository->delete($id);
            $jsonData = array('status'=>1);
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_user/add", name="add_user")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param Session $session
     * @return RedirectResponse|Response
     */
    public function add_user(Request $request, UserRepository $userRepository, Session $session) {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $tendangnhap = $request->get('tendangnhap');
            $hoten = $request->get('hoten');
            $email = $request->get('email');
            $sodt = $request->get('sodt');

            // Check sodt
            $pattern = "/^[0-9]+$/";
            if(!preg_match($pattern, $sodt) || strlen($sodt) > 13) $message = "Số điện thoại không hợp lệ";

            $ngaysinh = $request->get('ngaysinh');
            $gioitinh = $request->get('gioitinh');
            $matkhau = $request->get('matkhau');
            $sodu = $request->get('sodu');
            $status = $userRepository->check_add_user($tendangnhap,$email,$sodt);
            $message = "";
            if ($status == -1) $message = "Trùng tên đăng nhập";
            elseif ($status == -2) $message = "Trùng email";
            elseif ($status == - 3) $message = "Trùng số điện thoại";
            else {
                $userRepository->add($tendangnhap, md5($matkhau), $hoten, $ngaysinh, $email, $sodt, $gioitinh, $sodu);
                $session->set('message','Thêm user thành công!');
                return $this->redirectToRoute('quanly_user');
            }
            return $this->render('admin/form_user.html.twig', ['user'=>null,'message'=>$message]);
        }
        else {
            return $this->render('admin/form_user.html.twig', ['user'=>null,'message'=>""]);
        }
    }

    /**
     * @Route("/quanly_game", name="quanly_game")
     * @param Request $request
     * @param GameRepository $gameRepository
     * @param Session $session
     * @return JsonResponse|Response
     */
    public function show_game(Request $request, GameRepository $gameRepository, Session $session): Response
    {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        $count_row = $gameRepository->count_row("");
        $num_page = 10;
        if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
        else $num_page = (int)($count_row/10);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != null) {
                $count_row = $gameRepository->count_row($keyword);
                $list_game = $gameRepository->search($keyword,$page,10);
            }
            else {
                $list_game = $gameRepository->get_list($page,10);
            }
            if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
            else $num_page = (int)($count_row/10);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_game as $game) {
                $temp = array(
                    'id' => $game->getId(),
                    'tengame' => $game->getTengame(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_game = $gameRepository->get_list(1,10);
            $message = $session->get('message');
            $session->remove('message');
            return $this->render('admin/game.html.twig', [
                'list_game' => $list_game,'num_page'=>$num_page,'page'=>1,'message'=>$message,
            ]);
        }
    }

    /**
     * @Route("/quanly_game/edit/{id}", name="edit_game_admin", methods={"GET","POST"})
     * @param GameRepository $gameRepository
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function edit_game_admin(GameRepository $gameRepository, $id, Request $request, Session $session)
    {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $id = $request->get('id');
            $tengame = $request->get('tengame');
            $gameRepository->edit($id,$tengame);
            $this->session->set('message','Cập nhật game thành công!');
            return $this->redirectToRoute('quanly_game');
        }
        else {
            $game = $gameRepository->findOneBy(array('id'=>$id));
            return $this->render('admin/form_game.html.twig', ['game'=>$game,'message'=>""]);
        }
    }

    /**
     * @Route("/quanly_game/delete/{id}", name="delete_game", methods={"GET","POST"})
     * @param Request $request
     * @param GameRepository $gameRepository
     * @param NhiemvuRepository $nhiemvuRepository
     * @param $id
     * @param Session $session
     * @return JsonResponse|RedirectResponse
     */
    public function delete_game(Request $request, GameRepository $gameRepository, NhiemvuRepository $nhiemvuRepository, $id, Session $session) {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isXmlHttpRequest()) {
            $jsonData = array();
            if ($nhiemvuRepository->get_list_for_game($id))
            {
                $jsonData = array('status'=>0);
            }
            else {
                $gameRepository->delete($id);
                $jsonData = array('status'=>1);
            }
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_game/add", name="add_game")
     * @param Request $request
     * @param GameRepository $gameRepository
     * @param Session $session
     * @return Response
     */
    public function add_game(Request $request, GameRepository $gameRepository, Session $session) {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $tengame = $request->get('tengame');
            $gameRepository->add($tengame);
            $session->set('message',"Thêm game thành công!");
            return $this->redirectToRoute('quanly_game');
        }
        else {
            return $this->render('admin/form_game.html.twig', ['game'=>null,'message'=>""]);
        }
    }

    /**
     * @Route("/quanly_nhiemvu", name="quanly_nhiemvu")
     * @param Request $request
     * @param NhiemvuRepository $nhiemvuRepository
     * @param GameRepository $gameRepository
     * @param UserRepository $userRepository
     * @param Session $session
     * @return JsonResponse|Response
     */
    public function show_nhiemvu(Request $request, NhiemvuRepository $nhiemvuRepository, GameRepository $gameRepository, UserRepository $userRepository, Session $session): Response
    {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        $count_row = $nhiemvuRepository->count_row("");
        $num_page = 0;
        if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
        else $num_page = (int)($count_row/10);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != null) {
                $count_row = $nhiemvuRepository->count_row($keyword);
                $list_nhiemvu = $nhiemvuRepository->search($keyword,$page,10);
            }
            else {
                $list_nhiemvu = $nhiemvuRepository->get_list($page,10);
            }
            if ($count_row/10> (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
            else $num_page = (int)($count_row/10);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_nhiemvu as $nhiemvu) {
                $game = $nhiemvu->getGame();
                $user = $nhiemvu->getUser();
                $temp = array(
                    'id' => $nhiemvu->getId(),
                    'tengame' => $game->getTengame(),
                    'hoten' => $user->getHoten(),
                    'tieude' => $nhiemvu->getTieude(),
                    'noidung' => $nhiemvu->getNoidung(),
                    'ngaydang' => $nhiemvu->getNgaydang(),
                    'trangthai' => $nhiemvu->getTrangthai(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            if ($this->session->get('message')!=null) {
                $message = $this->session->get('message');
                $this->session->remove('message');
            }
            else $message = '';
            $list_nhiemvu = $nhiemvuRepository->get_list(1,10);
            return $this->render('admin/nhiemvu.html.twig', [
                'list_nhiemvu' => $list_nhiemvu,'num_page'=>$num_page,'page'=>1,'message'=>$message,
            ]);
        }
    }

    /**
     * @Route("/quanly_nhiemvu/duyet_nhiemvu/{id}", name="duyet_nhiemvu", methods={"GET","POST"})
     * @param Request $request
     * @param NhiemvuRepository $nhiemvuRepository
     */
    public function duyet_nhiemvu(Request $request, NhiemvuRepository $nhiemvuRepository, $id)
    {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isXmlHttpRequest()) {
            $status = $request->get("status");
            if ($status == '1')
                $nhiemvuRepository->duyet_nhiemvu($id,1);
            elseif ($status == '-1')
                $nhiemvuRepository->duyet_nhiemvu($id,-1);
            $nhiemvu = $nhiemvuRepository->findOneBy(['id'=>$id]);
            $jsonData = array();
            $idx = 0;

            if ($nhiemvu) {
                $game = $nhiemvu->getGame();
                $user = $nhiemvu->getUser();
                $temp = array(
                    'status'=>$status,
                    'id' => $nhiemvu->getId(),
                    'tengame' => $game->getTengame(),
                    'hoten' => $user->getHoten(),
                    'tieude' => $nhiemvu->getTieude(),
                    'noidung' => $nhiemvu->getNoidung(),
                    'ngaydang' => $nhiemvu->getNgaydang(),
                    'trangthai' => $nhiemvu->getTrangthai(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_nhiemvu/delete/{id}", name="delete_nhiemvu_admin", methods={"GET","POST"})
     * @param Request $request
     * @param NhiemvuRepository $nhiemvuRepository
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function delete_nhiemvu(Request $request, NhiemvuRepository $nhiemvuRepository, $id) {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $jsonData = array();
            $nhiemvuRepository->delete_nhiemvu($id);
            $jsonData = array('status'=>1);
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_nhiemvu/edit/{id}",name="edit_nhiemvu_admin",methods={"GET","POST"})
     * @param Request $request
     * @param NhiemvuRepository $nhiemvuRepository
     * @param $id
     * @param GameRepository $gameRepository
     * @return RedirectResponse|Response
     */
    public function edit_nhiemvu_admin(Request $request, NhiemvuRepository $nhiemvuRepository, $id, GameRepository $gameRepository) {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $game_id = $request->get('game_id');
            $tieude = $request->get('tieude');
            $noidung = $request->get('noidung');
            $trangthai = $request->get('trangthai');
            $trangthaicu = $request->get('trangthaicu');
            $nhiemvuRepository->edit_nhiemvu($id,$tieude,$noidung,$trangthai,$trangthaicu,$game_id);
            $this->session->set('message','Cập nhật nhiệm vụ thành công!');
            return $this->redirectToRoute('quanly_nhiemvu');
        }
        else {
            $nhiemvu = $nhiemvuRepository->findOneBy(['id'=>$id]);
            $list_game = $gameRepository->get_all();
            return $this->render('admin/form_nhiemvu.html.twig', ['nhiemvu'=>$nhiemvu,'list_game'=>$list_game]);
        }
    }

    /**
     * @Route("/quanly_thegame", name="quanly_thegame")
     * @param NhiemvuRepository $nhiemvuRepository
     * @return Response
     */
    public function show_thegame(ThegameRepository $thegameRepository, Request $request)
    {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        $count_row = $thegameRepository->count_row("");
        $num_page = 10;
        if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
        else $num_page = (int)($count_row/10);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != null) {
                $count_row = $thegameRepository->count_row($keyword);
                $list_thegame = $thegameRepository->search($keyword,$page,10);
            }
            else {
                $list_thegame = $thegameRepository->get_list($page,10);
            }
            if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
            else $num_page = (int)($count_row/10);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_thegame as $thegame) {
                $temp = array(
                    'id' => $thegame->getId(),
                    'tengame'=>$thegame->getGame()->getTengame(),
                    'seri' => $thegame->getSeri(),
                    'card_number' => $thegame->getCardnumber(),
                    'status' => $thegame->getStatus(),
                    'gia'=>$thegame->getGia(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_thegame = $thegameRepository->get_list(1,10);
            $message = $this->session->get('message');
            $this->session->remove('message');
            return $this->render('admin/thegame.html.twig', [
                'list_thegame' => $list_thegame,'num_page'=>$num_page,'page'=>1,'message'=>$message,
            ]);
        }
    }

    /**
     * @Route("/quanly_thegame/add",name="add_thegame")
     * @param Request $request
     * @param ThegameRepository $thegameRepository
     * @param GameRepository $gameRepository
     * @return RedirectResponse|Response
     */
    public function add_thegame(Request $request, ThegameRepository $thegameRepository, GameRepository $gameRepository)
    {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        $list_game = $gameRepository->get_all();
        if ($request->isMethod("POST")) {
            $seri = $request->get('seri');
            $cardnumber = $request->get('cardnumber');
            $game_id = $request->get('game_id');
            $gia = $request->get('gia');
            $message = "";
            // Check seri
            $pattern = "/^[0-9]+$/";
            if(!preg_match($pattern, $seri)) $message = "Số seri không hợp lệ";
            elseif (!preg_match($pattern, $cardnumber)) $message = "Mã số thẻ không hợp lệ";
            if ($message!="") {
                return $this->render('admin/form_thegame.html.twig', ['thegame'=>null,'message'=>$message,'list_game'=>$list_game]);
            }

            $thegameRepository->add($seri,$cardnumber,$game_id,$gia);
            $this->session->set('message',"Thêm thẻ game thành công!");
            return $this->redirectToRoute('quanly_thegame');
        }
        else {
            return $this->render('admin/form_thegame.html.twig', ['thegame'=>null,'message'=>"",'list_game'=>$list_game]);
        }
    }

    /**
     * @Route("/quanly_thegame/edit/{id}", name="edit_thegame")
     * @param Request $request
     * @param ThegameRepository $thegameRepository
     * @param $id
     * @param GameRepository $gameRepository
     * @return RedirectResponse|Response
     */
    public function edit_thegame(Request $request, ThegameRepository $thegameRepository,$id, GameRepository $gameRepository)
    {
        $current_admin = $this->session->get('admin');
        $thegame = $thegameRepository->findOneBy(['id'=>$id]);
        $list_game = $gameRepository->get_all();
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $seri = $request->get('seri');
            $cardnumber = $request->get('cardnumber');
            $game_id = $request->get('game_id');
            $gia = $request->get('gia');
            $message = "";
            // Check seri
            $pattern = "/^[0-9]+$/";
            if(!preg_match($pattern, $seri)) $message = "Số seri không hợp lệ";
            elseif (!preg_match($pattern, $cardnumber)) $message = "Mã số thẻ không hợp lệ";
            if ($message!="") {
                return $this->render('admin/form_thegame.html.twig', ['thegame'=>$thegame,'message'=>$message,'list_game'=>$list_game]);
            }

            $thegameRepository->edit($id,$seri,$cardnumber,$game_id,$gia);
            $this->session->set('message',"Cập nhật thẻ game thành công!");
            return $this->redirectToRoute('quanly_thegame');
        }
        else {
            return $this->render('admin/form_thegame.html.twig', ['thegame'=>$thegame,'message'=>"",'list_game'=>$list_game]);
        }
    }

    /**
     * @Route("/quanly_thegame/delete/{id}",name="delete_thegame")
     * @param Request $request
     * @param ThegameRepository $thegameRepository
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function delete_thegame(Request $request, ThegameRepository $thegameRepository, $id)
    {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isXmlHttpRequest()) {
            $jsonData = array('status'=>1);
            $thegameRepository->delete($id);
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_taikhoangame",name="quanly_taikhoangame")
     * @param Request $request
     * @param TaikhoangameRepository $taikhoangameRepository
     * @return JsonResponse|RedirectResponse|Response
     */
    public function show_tkgame(Request $request, TaikhoangameRepository $taikhoangameRepository) {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        $count_row = $taikhoangameRepository->count_row("");
        $num_page = 10;
        if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
        else $num_page = (int)($count_row/10);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != null) {
                $count_row = $taikhoangameRepository->count_row($keyword,0);
                $list_tkgame = $taikhoangameRepository->search($keyword,$page,10);
            }
            else {
                $list_tkgame = $taikhoangameRepository->get_list($page, 10);
            }
            if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
            else $num_page = (int)($count_row/10);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_tkgame as $tkgame) {
                $temp = array(
                    'id' => $tkgame->getId(),
                    'username' => $tkgame->getUsername(),
                    'ingame' => $tkgame->getIngame(),
                    'description' => $tkgame->getDescription(),
                    'tengame' => $tkgame->getGame()->getTengame(),
                    'gia'=>$tkgame->getGia(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_tkgame = $taikhoangameRepository->get_list(1,10);
            $message = $this->session->get('message');
            $this->session->remove('message');
            return $this->render('admin/taikhoangame.html.twig', [
                'list_tkgame' => $list_tkgame,'num_page'=>$num_page,'page'=>1,'message'=>$message,
            ]);
        }
    }

    /**
     * @Route("/quanly_taikhoangame/add",name="add_taikhoangame")
     * @param AnhRepository $anhRepository
     * @param GameRepository $gameRepository
     * @param TaikhoangameRepository $taikhoangameRepository
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @param Session $session
     * @return RedirectResponse|Response
     */
    public function add_tkgame(AnhRepository $anhRepository, GameRepository $gameRepository, TaikhoangameRepository $taikhoangameRepository, Request $request,string $uploadDir,
                               FileUploader $uploader, LoggerInterface $logger, Session $session) {
        $current_admin = $this->session->get('admin');
        $list_game = $gameRepository->get_all();
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $token = $request->get("token");

            if (!$this->isCsrfTokenValid('uploads', $token))
            {
                $logger->info("CSRF failure");
                $message = "Operation not allowed";
                return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>null,'message'=>$message,'list_game'=>$list_game]);
            }

            $file = $request->files->get('file');
            $game_id = $request->get("game_id");
            $username = $request->get('username');
            $ingame = $request->get('ingame');
            $password = $request->get('password');
            $description = $request->get('description');
            $gia = $request->get('gia');
            $status = $taikhoangameRepository->add($game_id,$username,$ingame,$password,$description,$gia);
            if ($status == -1 ) {
                $message = "Tên đăng nhập bị trùng!";
                return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>null,'message'=>$message,'list_game'=>$list_game]);
            }
            $this->session->set('message','Thêm tài khoản game thành công!');
            if (!empty($file)) {
                $allowed = array("jpg" => "images/jpg", "jpeg" => "images/jpeg", "gif" => "images/gif", "png" => "images/png");
                // Xác minh phần mở rộng tệp
                for ($i=0;$i<count($file);$i++) {
                    $filename = $file[$i]->getClientOriginalName();
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(!array_key_exists($ext, $allowed)) {
                        $message = "Lỗi: Định dạng file ảnh không hợp lệ!";
                        return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>null,'message'=>$message,'list_game'=>$list_game]);
                    }
                }
                $tkgame = $taikhoangameRepository->findOneBy(['username'=>$username,'game'=>$game_id]);
                for ($i=0;$i<count($file);$i++) {
                    $time = date("Y-m-d-H-i-s");
                    $filename = $file[$i]->getClientOriginalName();
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $filename = $username.'-'.$game_id.'-'.$time.'-'.$i.'.'.$ext;
                    $uploader->upload($uploadDir, $file[$i], $filename);
                    $anhRepository->add($filename,$tkgame->getId());
                }
            }

            return $this->redirectToRoute("quanly_taikhoangame");
        }
        else {
            return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>null,'message'=>"",'list_game'=>$list_game]);
        }
    }

    /**
     * @Route("/quanly_taikhoangame/delete/{id}", name="delete_taikhoangame", methods={"GET","POST"})
     * @param Request $request
     * @param AnhRepository $anhRepository
     * @param TaikhoangameRepository $taikhoangameRepository
     * @param $id
     * @param Session $session
     * @return JsonResponse|RedirectResponse
     */
    public function delete_tkgame(Request $request, AnhRepository $anhRepository, TaikhoangameRepository $taikhoangameRepository, $id, Session $session) {
        $current_admin = $session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isXmlHttpRequest()) {
            $anhRepository->delete_all_image($id);
            $taikhoangameRepository->delete($id);
            $jsonData = array('status'=>1);
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_taikhoangame/edit/{id}", name="edit_taikhoangame", methods={"GET","POST"})
     * @param AnhRepository $anhRepository
     * @param GameRepository $gameRepository
     * @param TaikhoangameRepository $taikhoangameRepository
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @param Session $session
     * @param $id
     * @return Response
     */
    public function edit_taikhoangame(AnhRepository $anhRepository, GameRepository $gameRepository, TaikhoangameRepository $taikhoangameRepository, Request $request,string $uploadDir,
                                      FileUploader $uploader, LoggerInterface $logger, Session $session, $id)
    {
        $current_admin = $session->get('admin');
        $list_game = $gameRepository->get_all();
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isMethod("POST")) {
            $token = $request->get("token");

            if (!$this->isCsrfTokenValid('uploads', $token))
            {
                $logger->info("CSRF failure");
                $message = "Operation not allowed";
                return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>null,'message'=>$message,'list_game'=>$list_game]);
            }

            $file = $request->files->get('file');
            $game_id = $request->get("game_id");
            $username = $request->get('username');
            $old_username = $request->get('old_username');
            $password = $request->get('password');
            $description = $request->get('description');
            $gia = $request->get('gia');
            $status = $taikhoangameRepository->edit($id,$game_id,$old_username,$username,$password,$description,$gia);
            if ($status == -1 ) {
                $message = "Tên đăng nhập bị trùng!";
                return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>null,'message'=>$message,'list_game'=>$list_game]);
            }
            $this->session->set('message','Cập nhật tài khoản game thành công!');
            if (!empty($file)) {
                $allowed = array("jpg" => "images/jpg", "jpeg" => "images/jpeg", "gif" => "images/gif", "png" => "images/png");
                // Xác minh phần mở rộng tệp
                for ($i=0;$i<count($file);$i++) {
                    $filename = $file[$i]->getClientOriginalName();
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(!array_key_exists($ext, $allowed)) {
                        $message = "Lỗi: Định dạng file ảnh không hợp lệ!";
                        $tkgame = $taikhoangameRepository->findOneBy(array('id'=>$id));
                        return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>$tkgame,'message'=>$message,'list_game'=>$list_game]);
                    }
                }
                $tkgame = $taikhoangameRepository->findOneBy(['username'=>$username,'game'=>$game_id]);
                for ($i=0;$i<count($file);$i++) {
                    $time = date("Y-m-d-H-i-s");
                    $filename = $file[$i]->getClientOriginalName();
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $filename = $username.'-'.$game_id.'-'.$time.'-'.$i.'.'.$ext;
                    $uploader->upload($uploadDir, $file[$i], $filename);
                    $anhRepository->add($filename,$tkgame->getId());
                }
            }

            return $this->redirectToRoute("quanly_taikhoangame");
        }
        else {
            $tkgame = $taikhoangameRepository->findOneBy(array('id'=>$id));
            return $this->render('admin/form_taikhoangame.html.twig', ['taikhoangame'=>$tkgame,'message'=>"",'list_game'=>$list_game]);
        }
    }

    /**
     * @Route("/quanly_taikhoangame/images/{id}",name="image_taikhoangame")
     * @param AnhRepository $anhRepository
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function get_image(AnhRepository $anhRepository, Request $request, $id) {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isXmlHttpRequest()) {
            $list_image = $anhRepository->findBy(['tkgame'=>$id]);
            $jsonData = array();
            $idx = 0;

            foreach($list_image as $image) {
                $temp = array(
                    'id' => $image->getId(),
                    'link' => $image->getLink(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/quanly_taikhoangame/delete-image/{id}",name="delete_image_taikhoangame")
     * @param Request $request
     * @param AnhRepository $anhRepository
     * @param $id
     * @return JsonResponse|RedirectResponse
     */
    public function delete_image_taikhoangame(Request $request, AnhRepository $anhRepository, $id)
    {
        $current_admin = $this->session->get('admin');
        if ($current_admin == null) return $this->redirectToRoute('admin_login');
        if ($request->isXmlHttpRequest()) {
            $jsonData = array('status'=>1);
            $anhRepository->delete($id);
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/test",name="test")
     * @param TaikhoangameRepository $taikhoangameRepository
     */
    public function test(TaikhoangameRepository $taikhoangameRepository) {
        return new Response('ádasd');
    }
}
