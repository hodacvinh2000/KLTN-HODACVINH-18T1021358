<?php

namespace App\Controller;

use App\Repository\AnhRepository;
use App\Repository\BinhluannhiemvuRepository;
use App\Repository\GameRepository;
use App\Repository\TaikhoangameRepository;
use App\Repository\ThegameRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NhiemvuRepository;
use Symfony \ Component \ HttpFoundation \ JsonResponse;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class TrangchuController extends AbstractController
{
    private $requestStack;
    private $current_user;
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->session = $requestStack->getSession();
        $this->current_user = $this->session->get('current_user');
    }

    /**
     * @Route("/login",name="login_user")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    public function login_user(UserRepository $userRepository, Request $request) {
        if ($request->isMethod("POST")) {
            $username = $request->get('username');
            $password = $request->get('pass');
            $user = $userRepository->check_login($username,md5($password));
            if ($user) {
                if ($user->getQuyen() === -1) {
                    $message = "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ với quản trị website!";
                    return $this->render('trangchu/login.html.twig',['message'=>$message]);
                }
                else {
                    $this->session->set('current_user',$user);
                    return $this->redirectToRoute('trangchu');
                }
            }
            $message = "Sai tên tài khoản hoặc mật khẩu!";
            return $this->render('trangchu/login.html.twig',['message'=>$message]);
        }
        else {
            return $this->render('trangchu/login.html.twig',['message'=>""]);
        }
    }

    /**
     * @Route("logout_user",name="logout_user")
     * @param NhiemvuRepository $nhiemvuRepository
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function logout_user(Request $request) {
        $this->session->remove('current_user');
        return $this->redirectToRoute('trangchu');
    }

    /**
     * @Route("/", name="trangchu")
     * @param NhiemvuRepository $nhiemvuRepository
     * @param Request $request
     */
    public function show_trangchu(NhiemvuRepository $nhiemvuRepository, Request $request) {
        $count_row = $nhiemvuRepository->count_row("");
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
                $list_nhiemvu = $nhiemvuRepository->get_list_active($page,10);
            }
            if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
            else $num_page = (int)($count_row/10);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_nhiemvu as $nhiemvu) {
                $temp = array(
                    'id'=>$nhiemvu->getId(),
                    'tieude'=>$nhiemvu->getTieude(),
                    'ngaydang'=>$nhiemvu->getNgaydang(),
                    'hoten'=>$nhiemvu->getUser()->getHoten(),
                    'tengame'=>$nhiemvu->getGame()->getTengame(),
                    'noidung'=>$nhiemvu->getNoidung(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_nhiemvu = $nhiemvuRepository->get_list_active(1,10);
            return $this->render('trangchu/index.html.twig', ['list_nhiemvu' => $list_nhiemvu,'num_page'=>$num_page]);
        }
    }

    /**
     * @Route("/chitietnhiemvu/{id}", name="chitietnhiemvu")
     * @param BinhluannhiemvuRepository $binhluannhiemvuRepository
     * @param NhiemvuRepository $nhiemvuRepository
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function info_mission(BinhluannhiemvuRepository $binhluannhiemvuRepository, NhiemvuRepository $nhiemvuRepository, Request $request, $id) {
        if ($this->session->get('current_user')==null) return $this->redirectToRoute('login_user');
        $nhiemvu = $nhiemvuRepository->findOneBy(['id'=>$id]);
        $list_binhluan = $binhluannhiemvuRepository->danhsach_binhluan($id);
        return $this->render('trangchu/chitietnhiemvu.html.twig',['nhiemvu'=>$nhiemvu,'list_binhluan'=>$list_binhluan]);
    }

    /**
     * @Route("/xemphanhoi/{id}",name="xemphanhoi")
     * @param Request $request
     * @param BinhluannhiemvuRepository $binhluannhiemvuRepository
     * @param $id
     */
    public function xemphanhoi(Request $request, BinhluannhiemvuRepository $binhluannhiemvuRepository, $id) {
        if ($this->session->get('current_user')==null) return $this->redirectToRoute('login_user');
        if ($request->isXmlHttpRequest()) {
            $list_phanhoi = $binhluannhiemvuRepository->danhsach_phanhoi($id);
            $jsonData = array();
            $idx = 0;

            foreach($list_phanhoi as $phanhoi) {
                $temp = array(
                    'id'=>$phanhoi->getId(),
                    'nguoiduocphanhoi'=>$phanhoi->getPhanhoi()->getNguoibinhluan()->getId(),
                    'tennguoiduocphanhoi'=>$phanhoi->getPhanhoi()->getNguoibinhluan()->getHoten(),
                    'idnguoibinhluan'=>$phanhoi->getNguoibinhluan()->getId(),
                    'hoten'=>$phanhoi->getNguoibinhluan()->getHoten(),
                    'binhluan'=>$phanhoi->getBinhluan(),
                    'sophanhoi'=>$phanhoi->getSophanhoi(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/traloibinhluan/{id}")
     * @param Request $request
     * @param BinhluannhiemvuRepository $binhluannhiemvuRepository
     * @param $id
     */
    public function traloibinhluan(Request $request, BinhluannhiemvuRepository $binhluannhiemvuRepository, $id) {
        if ($this->session->get('current_user')==null) return $this->redirectToRoute('login_user');
        if ($request->isXmlHttpRequest()) {
            $content = $request->get('content');
            $current_user_id = $request->get('current_user_id');
            $binhluannhiemvuRepository->traloibinhluan($id,$current_user_id,$content);
            $list_phanhoi = $binhluannhiemvuRepository->get_new_phanhoi($id,$current_user_id);
            $phanhoi = $list_phanhoi[0];
            $binhluan = $binhluannhiemvuRepository->findOneBy(['id'=>$id]);
            $jsonData = array(
                'id'=>$phanhoi->getId(),
                'nguoiduocphanhoi'=>$phanhoi->getPhanhoi()->getNguoibinhluan()->getId(),
                'tennguoiduocphanhoi'=>$phanhoi->getPhanhoi()->getNguoibinhluan()->getHoten(),
                'idnguoibinhluan'=>$phanhoi->getNguoibinhluan()->getId(),
                'hoten'=>$phanhoi->getNguoibinhluan()->getHoten(),
                'binhluan'=>$phanhoi->getBinhluan(),
                'sophanhoi'=>$phanhoi->getSophanhoi(),
                'sophanhoi_parent'=>$binhluan->getSophanhoi(),
            );
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/binhluan/{id}",name="binhluan")
     * @param NhiemvuRepository $nhiemvuRepository
     * @param BinhluannhiemvuRepository $binhluannhiemvuRepository
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function binhluan(NhiemvuRepository $nhiemvuRepository, BinhluannhiemvuRepository $binhluannhiemvuRepository, Request $request, $id) {
        if ($this->session->get('current_user')==null) return $this->redirectToRoute('login_user');
        if ($request->isXmlHttpRequest()) {
            $content = $request->get('content');
            $current_user_id = $request->get('current_user_id');
            $binhluannhiemvuRepository->thembinhluan($id,$current_user_id,$content);
            $list_phanhoi = $binhluannhiemvuRepository->get_new_binhluan($id,$current_user_id);
            $phanhoi = $list_phanhoi[0];
            $jsonData = array(
                'id'=>$phanhoi->getId(),
                'idnguoibinhluan'=>$phanhoi->getNguoibinhluan()->getId(),
                'hoten'=>$phanhoi->getNguoibinhluan()->getHoten(),
                'binhluan'=>$phanhoi->getBinhluan(),
                'sophanhoi'=>$phanhoi->getSophanhoi(),
            );
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/xoabinhluan/{id}",name="xoa_binhluan")
     * @param BinhluannhiemvuRepository $binhluannhiemvuRepository
     * @param Request $request
     * @param $id
     */
    public function xoabinhluan(BinhluannhiemvuRepository $binhluannhiemvuRepository, Request $request, $id) {
        if ($this->session->get('current_user')==null) return $this->redirectToRoute('login_user');
        if ($request->isXmlHttpRequest()) {
            $binhluan_answered = $binhluannhiemvuRepository->findOneBy(['id'=>$binhluannhiemvuRepository->findOneBy(['id'=>$id])->getPhanhoi()->getId()]);
            $binhluannhiemvuRepository->xoabinhluan($id);
            $jsonData = array('status'=>1,'sophanhoi_parent'=>$binhluan_answered->getSophanhoi(),'id_parent'=>$binhluan_answered->getId());
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/taikhoangame",name="list_account")
     * @param GameRepository $gameRepository
     * @param Request $request
     */
    public function list_game(GameRepository $gameRepository, Request $request) {
        if ($this->current_user == null) return $this->redirectToRoute('login_user');
        $count_row = $gameRepository->count_row("");
        $num_page = 8;
        if ($count_row/8 > (float)((int)($count_row/8))) $num_page = (int)($count_row/8)+1;
        else $num_page = (int)($count_row/8);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != null) {
                $count_row = $gameRepository->count_row($keyword);
                $list_game = $gameRepository->search($keyword,$page,8);
            }
            else {
                $list_game = $gameRepository->get_list($page,8);
            }
            if ($count_row/8 > (float)((int)($count_row/8))) $num_page = (int)($count_row/8)+1;
            else $num_page = (int)($count_row/8);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_game as $game) {
                $temp = array(
                    'id' => $game->getId(),
                    'tengame' => $game->getTengame(),
                    'anh'=>$game->getAnh(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_game = $gameRepository->get_list(1,8);
            return $this->render('trangchu/danhsachgame.html.twig', [
                'list_game' => $list_game,'num_page'=>$num_page,'page'=>1]);
        }
    }

    /**
     * @Route("/taikhoangame/{id}",name="danhsach_tkgame")
     * @param TaikhoangameRepository $taikhoangameRepository
     * @param $id
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function danhsach_tkgame(TaikhoangameRepository $taikhoangameRepository, $id, Request $request){
        $count_row = $taikhoangameRepository->count_row_game($id,"");
        if ($count_row/8 > (float)((int)($count_row/8))) $num_page = (int)($count_row/8)+1;
        else $num_page = (int)($count_row/8);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != "") {
                $list_tkgame = $taikhoangameRepository->search_game($id,$keyword,$page,8);
                $count_row = $taikhoangameRepository->count_row_game($id,$keyword);
            }
            else {
                $list_tkgame = $taikhoangameRepository->get_list_game($id,$page,8);
            }
            if ($count_row/8 > (float)((int)($count_row/8))) $num_page = (int)($count_row/8)+1;
            else $num_page = (int)($count_row/8);
            $jsonData = array();
            $jsonData[0] = array('num_page'=>$num_page,'page'=>$page);
            $idx = 1;

            foreach($list_tkgame as $tkgame) {
                $temp = array(
                    'id' => $tkgame->getId(),
                    'ingame'=>$tkgame->getIngame(),
                    'anhgame'=>$tkgame->getGame()->getAnh(),
                    'gia'=>$tkgame->getGia(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_tkgame = $taikhoangameRepository->get_list_game($id,1,8);
            return $this->render('trangchu/taikhoangame.html.twig', [
                'list_tkgame' => $list_tkgame,'num_page'=>$num_page,'page'=>1,'id'=>$id]);
        }
    }

    /**
     * @Route("/chitiettaikhoan/{id}", name="account_info")
     * @param TaikhoangameRepository $taikhoangameRepository
     * @param AnhRepository $anhRepository
     * @param $id
     * @return Response
     */
    public function account_info(TaikhoangameRepository $taikhoangameRepository, AnhRepository $anhRepository, $id) {
        $account = $taikhoangameRepository->findOneBy(['id'=>$id]);
        $list_anh = $anhRepository->get_list_account($id);
        return $this->render('trangchu/chitiettaikhoan.html.twig',['account'=>$account, 'list_anh'=>$list_anh,"message"=>"","non_vertical"=>false]);
    }

    /**
     * @Route("/sendVerticalEmail", name="verticalEmail")
     * @param UserRepository $userRepository
     * @param $id
     * @param Request $request
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendVerticalLink(UserRepository $userRepository, Request $request) {
        if ($this->current_user == null) return $this->redirectToRoute('login_user');
        $id = $this->current_user->getId();
        $user = $userRepository->findOneBy(['id'=>$id]);
        if ($request->isMethod('POST')) {
            $code = $request->get('code');
            if($code == $user->getVerticalCode()) {
                $userRepository->activeUser($id);
                $user = $userRepository->findOneBy(['id'=>$id]);
                $this->session->set('current_user',$user);
                $this->session->set('message','Tài khoản đã được kích hoạt');
                return $this->redirectToRoute('my_info');
            }
            else {
                $message = "Mã kích hoạt không đúng!";
                return $this->render('trangchu/verticalForm.html.twig',['message'=>$message]);
            }
        }
        else {
            return $this->render('trangchu/verticalForm.html.twig',['message'=>""]);
        }
    }

    /**
     * @Route("/sendCode",name="sendCode")
     * @param UserRepository $userRepository
     * @param TaikhoangameRepository $taikhoangameRepository
     * @param AnhRepository $anhRepository
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendCode(UserRepository $userRepository) {
        $id = $this->current_user->getId();
        $user = $userRepository->findOneBy(['id'=>$id]);
        //Khởi tạo transport
        $transport = Transport::fromDsn('smtp://hodacvinh2000@gmail.com:qlzhtlerlpyknxwr@smtp.gmail.com:25');

        //Khởi tạo đối tượng mailer và email
        $mailer   = new Mailer($transport);
        $email    = new Email();
        //Khai báo các thông tin gửi mail
        $email->from('hodacvinh2000@gmail.com')
            ->to($user->getEmail())
            ->subject('Mail subject')
            ->text('Mã kích hoạt của bạn: '.md5($user->getTendangnhap().''.$user->getId()));
        //Thực hiện gửi mail
        $mailer->send($email);
        $userRepository->setVerticalCode($id,md5($user->getTendangnhap().''.$user->getId()));
        return new JsonResponse(['status'=>'oke']);
    }

    /**
     * @Route("/mua-taikhoan/{id}",name="buy_account")
     * @param UserRepository $userRepository
     * @param TaikhoangameRepository $taikhoangameRepository
     * @param AnhRepository $anhRepository
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function buy_account(UserRepository $userRepository, TaikhoangameRepository $taikhoangameRepository, AnhRepository $anhRepository, $id) {
        if ($this->current_user == null) return $this->redirectToRoute('login_user');
        $account = $taikhoangameRepository->findOneBy(['id'=>$id]);
        $list_anh = $anhRepository->get_list_account($id);
        if ($this->current_user->getQuyen() != 1) {
            return $this->render('trangchu/chitiettaikhoan.html.twig',['account'=>$account, 'list_anh'=>$list_anh,'message'=>"","non_vertical"=>true]);
        }
        elseif ($this->current_user->getSodu() < $account->getGia()) {
            return $this->render('trangchu/chitiettaikhoan.html.twig',['account'=>$account, 'list_anh'=>$list_anh,'message'=>"Số dư của bạn không đủ. Hãy nạp thêm tiền!","non_vertical"=>false]);
        }
        elseif ($account->getStatus() == 1) {
            //Khởi tạo transport
            $transport = Transport::fromDsn('smtp://hodacvinh2000@gmail.com:qlzhtlerlpyknxwr@smtp.gmail.com:25');
            $user = $this->current_user;
            //Khởi tạo đối tượng mailer và email
            $mailer   = new Mailer($transport);
            $email    = new Email();
            //Khai báo các thông tin gửi mail
            $email->from('hodacvinh2000@gmail.com')
                ->to($user->getEmail())
                ->subject('THÔNG TIN TÀI KHOẢN CỦA BẠN')
                ->html('<h2>Thông tin tài khoản của bạn</h2>
                        <p>Tên đăng nhập: '.$account->getUsername().'</p><p>Mật khẩu: '.$account->getPassword().'</p>
                        <p>Ingame: '.$account->getIngame().'</p><p>Game: '.$account->getGame()->getTengame().'</p>
                        <p>Mô tả: '.$account->getDescription().'</p>');
            //Thực hiện gửi mail
            $mailer->send($email);
            $taikhoangameRepository->setStatus($account->getId(),0);
            // Cập nhật số dư và current_user
            $userRepository->setSodu($user->getId(),$user->getSodu()-$account->getGia());
            $current_user = $userRepository->findOneBy(['id'=>$user->getId()]);
            $this->session->set('current_user',$current_user);
            return $this->render('trangchu/thankyou.html.twig');
        }
        else {
            return $this->render('trangchu/account-bought-before.html.twig');
        }
    }

    /**
     * @Route("/card-game",name="card-game")
     * @param ThegameRepository $thegameRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function card_game(ThegameRepository $thegameRepository, Request $request) {
        if ($this->current_user == null) return $this->redirectToRoute('login_user');
        if ($request->isXmlHttpRequest()) {
            $selectedGame = $request->get('selectedGame');
            $jsonData = array();
            $idx = 0;
            $list_gia = $thegameRepository->get_list_gia($selectedGame);
            foreach($list_gia as $gia) {
                $temp = array(
                    'gia'=>$gia->getGia(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $message = "";
            $list_game = $thegameRepository->get_list_game();
            $list_gia = $thegameRepository->get_list_gia($list_game[0]->getGame()->getId());
            return $this->render('trangchu/thegame.html.twig',['message'=>$message,'list_game'=>$list_game,'list_gia'=>$list_gia]);
        }
    }

    /**
     * @Route("/buy-cardgame",name="buy-cardgame")
     * @param UserRepository $userRepository
     * @param ThegameRepository $thegameRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function buy_cardgame(UserRepository $userRepository, ThegameRepository $thegameRepository, Request $request) {
        if ($this->current_user == null) return $this->redirectToRoute('login_user');
        if ($request->isMethod('POST')) {
            $game_id = $request->get('game_id');
            $gia = $request->get('gia');
            $list_thegame = $thegameRepository->get_list_by_game_and_gia($game_id,$gia);
            if(count($list_thegame) > 0) {
                $thegame = $list_thegame[0];
                if ($this->current_user->getQuyen() != 1) {
                    $message = "Tài khoản chưa được kích hoạt. Vui lòng kích hoạt để mua thẻ game!";
                    $list_game = $thegameRepository->get_list_game();
                    $list_gia = $thegameRepository->get_list_gia($list_game[0]->getGame()->getId());
                    return $this->render('trangchu/thegame.html.twig',['message'=>$message,'list_game'=>$list_game,'list_gia'=>$list_gia]);
                }
                if ($this->current_user->getSodu() >= $thegame->getGia()) {
                    $user = $this->current_user;
                    $thegameRepository->setStatus($thegame->getId(),0);
                    $userRepository->setSodu($user,$user->getSodu()-$thegame->getGia());
                    //Khởi tạo transport
                    $transport = Transport::fromDsn('smtp://hodacvinh2000@gmail.com:qlzhtlerlpyknxwr@smtp.gmail.com:25');
                    //Khởi tạo đối tượng mailer và email
                    $mailer   = new Mailer($transport);
                    $email    = new Email();
                    //Khai báo các thông tin gửi mail
                    $email->from('hodacvinh2000@gmail.com')
                        ->to($user->getEmail())
                        ->subject('THÔNG TIN THẺ GAME CỦA BẠN!')
                        ->html('<h2>Thông tin thẻ game</h2>
                                <p>Game: '.$thegame->getGame()->getTengame().'</p>
                                <p>Số seri: '.$thegame->getSeri().'</p>
                                <p>Số thẻ: '.$thegame->getCardnumber().'</p>');
                    //Thực hiện gửi mail
                    $mailer->send($email);
                    $current_user = $userRepository->findOneBy(['id'=>$user->getId()]);
                    $this->session->set('current_user',$current_user);
                    return $this->render('trangchu/thankyou.html.twig');
                }
                else {
                    $message = "Số dư của bạn không đủ để mua tài khoản này!";
                    $list_game = $thegameRepository->get_list_game();
                    $list_gia = $thegameRepository->get_list_gia($list_game[0]->getGame()->getId());
                    return $this->render('trangchu/thegame.html.twig',['message'=>$message,'list_game'=>$list_game,'list_gia'=>$list_gia]);
                }
            }
            else {
                $message = "Thẻ game với mệnh giá này đã hết ! Vui lòng chọn mệnh giá khác!";
                $list_game = $thegameRepository->get_list_game();
                $list_gia = $thegameRepository->get_list_gia($list_game[0]->getGame()->getId());
                return $this->render('trangchu/thegame.html.twig',['message'=>$message,'list_game'=>$list_game,'list_gia'=>$list_gia]);
            }
        }
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserRepository $userRepository
     */
    public function register(Request $request, UserRepository $userRepository) {
        if ($request->isMethod("POST")) {
            $message = "";
            $username = $request->get('username');
            $password = $request->get('password');
            $confirmPassword = $request->get('confirmPassword');
            $name = $request->get('name');
            $birthday = $request->get('birthday');
            $email = $request->get('email');
            $phone = $request->get('phone');
            $sex = $request->get('sex');
            switch ($userRepository->check_add_user($username,$email,$phone)) {
                case -1:
                    $message = "Trùng tên đăng nhập";
                    return $this->render("trangchu/register.html.twig",['message'=>$message]);
                case -2:
                    $message = "Trùng email";
                    return $this->render("trangchu/register.html.twig",['message'=>$message]);
                case -3:
                    $message = "Trùng số điện thoại";
                    return $this->render("trangchu/register.html.twig",['message'=>$message]);
            }
            if ($password != $confirmPassword) {
                $message = "Mật khẩu không trùng khớp";
                return $this->render("trangchu/register.html.twig",['message'=>$message]);
            }
            $userRepository->add($username,$password,$name,$birthday,$email,$phone,$sex,0);
            $this->session->set('current_user',$userRepository->findOneBy(['tendangnhap'=>$username]));
            return $this->redirectToRoute('trangchu');
        }
        else {
            return $this->render("trangchu/register.html.twig",['message'=>""]);
        }
    }

    /**
     * @Route("/getPassword",name="getPassword")
     * @param Request $request
     * @param UserRepository $userRepository
     */
    public function getPass(Request $request, UserRepository $userRepository) {
        if($request->isMethod('POST')) {
            $username = $request->get('username');
            $user = $userRepository->findOneBy(['tendangnhap'=>$username]);
            if ($user) {
                $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
                $newPassword = substr(str_shuffle($data), 0, 7);
                $md5Password = md5($newPassword);
                $userRepository->setPassword($user->getId(),$md5Password);
                $user = $userRepository->findOneBy(['tendangnhap'=>$username]);
                //Khởi tạo transport
                $transport = Transport::fromDsn('smtp://hodacvinh2000@gmail.com:qlzhtlerlpyknxwr@smtp.gmail.com:25');

                //Khởi tạo đối tượng mailer và email
                $mailer   = new Mailer($transport);
                $email    = new Email();
                //Khai báo các thông tin gửi mail
                $email->from('hodacvinh2000@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Mật khẩu của bạn!!!')
                    ->html('<p>Mật khẩu: </p>'.$newPassword);
                //Thực hiện gửi mail
                $mailer->send($email);
                return $this->render('trangchu/getPassword.html.twig',['message'=>"Mật khẩu đã được gửi về email của bạn!"]);
            }
            else {
                $message = "không tồn tại tài khoản này!";
                return $this->render('trangchu/getPassword.html.twig',['message'=>$message]);
            }
        }
        else {
            return $this->render('trangchu/getPassword.html.twig',['message'=>""]);
        }
    }

}
