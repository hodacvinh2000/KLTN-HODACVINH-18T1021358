<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/thongtincanhan")
 */
class UserController extends AbstractController
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
     * @route("/",name="my_info")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function my_info(UserRepository $userRepository, Request $request){
        if ($this->current_user == null) return $this->redirectToRoute('login');
        $message = "";
        if($this->session->get('message')!=null) {
            $message = $this->session->get('message');
            $this->session->remove('message');
        }
        return $this->render('user/index.html.twig',['user'=>$this->current_user,'message'=>$message]);
    }

    /**
     * @Route("/update",name="update_user")
     * @param UserRepository $userRepository
     * @param Request $request
     */
    public function update_user(UserRepository $userRepository, Request $request) {
        if ($this->current_user == null) return $this->redirectToRoute('login');
        $message = "";
        if($request->isMethod("POST")) {
            $hoten = $request->get('hoten');
            $ngaysinh = $request->get('ngaysinh');
            $email = $request->get('email');
            $oldemail = $request->get('oldemail');
            $sodt = $request->get('sodt');
            $oldsodt = $request->get('oldsodt');
            $gioitinh = $request->get('gioitinh');

            $change_email = 0;
            if ($oldemail != $email) $change_email = 1;
            // Check sodt
            $pattern = "/^[0-9]+$/";
            if(!preg_match($pattern, $sodt) || strlen($sodt) > 13) $message = "Số điện thoại không hợp lệ";
            $status = $userRepository->check_edit_user($oldemail,$email,$oldsodt,$sodt);
            if ($status == -1) $message = "Trùng email";
            elseif ($status == -2) $message = "Trùng số điện thoại";
            if ($message == ""){
                $userRepository->update_user($this->current_user->getId(),$hoten,$ngaysinh,$email,$sodt,$gioitinh);
                if ($change_email === 1) $userRepository->return_status($this->current_user->getId());
                $this->session->set('current_user',$userRepository->findOneBy(['id'=>$this->current_user->getId()]));
                $this->session->set('message','Cập nhật thành công!');
                return $this->redirectToRoute('my_info');
            }
            else {
                return $this->render('user/index.html.twig',['user'=>$this->current_user,'message'=>$message]);
            }
        }
    }

    /**
     * @Route("/doimatkhau",name="change_password")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function change_password(UserRepository $userRepository, Request $request) {
        if ($this->current_user == null) return $this->redirectToRoute('login');
        if($request->isMethod('POST')) {
            $old_password = $request->get('old_password');
            $new_password1 = $request->get('new_password1');
            $new_password2 = $request->get('new_password2');
            $message = $userRepository->change_password($this->current_user->getId(),md5($old_password),$new_password1,$new_password2);
            if ($message == "Đổi mật khẩu thành công!") {
                $this->session->set('message',$message);
                return $this->redirectToRoute('my_info');
            }
            else {
                return $this->render('user/doimatkhau.html.twig',['message'=>$message]);
            }
        }
        return $this->render('user/doimatkhau.html.twig',['message'=>""]);
    }
}
