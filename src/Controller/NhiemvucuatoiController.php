<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\NhiemvuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NhiemvucuatoiController
 * @package App\Controller
 * @Route("/nhiemvucuatoi")
 */
class NhiemvucuatoiController extends AbstractController
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
     * @Route("/",name="my_mission")
     * @param NhiemvuRepository $nhiemvuRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function show_my_mission(NhiemvuRepository $nhiemvuRepository, Request $request) {
        if ($this->current_user == null) return $this->redirectToRoute('login_user');
        $message = '';
        if ($this->session->get('message')) {
            $message = $this->session->get('message');
            $this->session->remove('message');
        }
        $count_row = $nhiemvuRepository->count_row_for_user("",$this->current_user->getId());
        $num_page = 0;
        if ($count_row/10 > (float)((int)($count_row/10))) $num_page = (int)($count_row/10)+1;
        else $num_page = (int)($count_row/10);
        if ($request->isXmlHttpRequest()) {
            $page = $request->get("page");
            $keyword = $request->get("keyword");
            if ($keyword != null) {
                $count_row = $nhiemvuRepository->count_row_for_user($keyword,$this->current_user->getId());
                $list_nhiemvu = $nhiemvuRepository->search_for_user($keyword,$page,10,$this->current_user->getId());
            }
            else {
                $list_nhiemvu = $nhiemvuRepository->get_list_for_user($this->current_user->getId(),$page,10);
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
                    'tengame'=>$nhiemvu->getGame()->getTengame(),
                    'noidung'=>$nhiemvu->getNoidung(),
                    'ngaydang'=>$nhiemvu->getNgaydang(),
                    'trangthai'=>$nhiemvu->getTrangthai(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
        else {
            $list_nhiemvu = $nhiemvuRepository->get_list_for_user($this->current_user->getId(),1,10);
            return $this->render('nhiemvucuatoi/index.html.twig', ['list_nhiemvu' => $list_nhiemvu,'num_page'=>$num_page,'message'=>$message]);
        }
    }

    /**
     * @Route("/add",name="add_mission_user")
     * @param Request $request
     * @param BanggiaRepository $banggiaRepository
     * @param GameRepository $gameRepository
     * @param NhiemvuRepository $nhiemvuRepository
     * @return Response
     */
    public function add_mission_user(Request $request, GameRepository $gameRepository, NhiemvuRepository $nhiemvuRepository) {
        if ($this->current_user == null) return $this->redirectToRoute('login');
        $list_game = $gameRepository->get_all();
        if ($request->isMethod('POST')) {
            $tieude = $request->get('tieude');
            $game_id = $request->get('game_id');
            $noidung = $request->get('noidung');
            $nhiemvuRepository->add_nhiemvu($this->current_user->getId(),$tieude,$game_id,$noidung);
            $this->session->set('message','Thêm nhiệm vụ thành công!');
            return $this->redirectToRoute('my_mission');
        }
        else {
            return $this->render('nhiemvucuatoi/formnhiemvu.html.twig',['list_game'=>$list_game,'nhiemvu'=>null,'message'=>""]);
        }
    }

    /**
     * @Route("/edit/{id}",name="edit_mission_user")
     * @param Request $request
     * @param BanggiaRepository $banggiaRepository
     * @param GameRepository $gameRepository
     * @param NhiemvuRepository $nhiemvuRepository
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit_mission_user(Request $request, GameRepository $gameRepository, NhiemvuRepository $nhiemvuRepository, $id) {
        if ($this->current_user == null) return $this->redirectToRoute('login');
        $list_game = $gameRepository->get_all();
        if ($request->isMethod('POST')) {
            $tieude = $request->get('tieude');
            $game_id = $request->get('game_id');
            $noidung = $request->get('noidung');
            $nhiemvuRepository->update_nhiemvu_user($id,$tieude,$game_id,$noidung);
            $this->session->set('message','Cập nhật nhiệm vụ thành công!');
            return $this->redirectToRoute('my_mission');
        }
        else {
            $nhiemvu = $nhiemvuRepository->findOneBy(['id'=>$id]);
            return $this->render('nhiemvucuatoi/formnhiemvu.html.twig',['list_game'=>$list_game,'nhiemvu'=>$nhiemvu,'message'=>""]);
        }
    }

    /**
     * @Route("/cancel/{id}",name="cancel_mission")
     * @param NhiemvuRepository $nhiemvuRepository
     * @param Request $request
     * @param $id
     */
    public function cancel_mission(NhiemvuRepository $nhiemvuRepository, Request $request, $id) {
        if ($this->current_user==null) return $this->redirectToRoute('login');
        if ($request->isXmlHttpRequest()) {
            $nhiemvuRepository->cancel_mission($id);
            $nhiemvu = $nhiemvuRepository->findOneBy(['id'=>$id]);
            $jsonData = array(
                'id'=>$nhiemvu->getId(),
                'tieude'=>$nhiemvu->getTieude(),
                'tengame'=>$nhiemvu->getGame()->getTengame(),
                'noidung'=>$nhiemvu->getNoidung(),
                'ngaydang'=>$nhiemvu->getNgaydang(),
                'trangthai'=>$nhiemvu->getTrangthai(),
            );
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/complete/{id}",name="complete_mission")
     * @param NhiemvuRepository $nhiemvuRepository
     * @param Request $request
     * @param $id
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function complete_mission(NhiemvuRepository $nhiemvuRepository, Request $request, $id) {
        if ($this->current_user==null) return $this->redirectToRoute('login');
        if ($request->isXmlHttpRequest()) {
            $nhiemvuRepository->complete_mission($id);
            $nhiemvu = $nhiemvuRepository->findOneBy(['id'=>$id]);
            $jsonData = array(
                'id'=>$nhiemvu->getId(),
                'tieude'=>$nhiemvu->getTieude(),
                'tengame'=>$nhiemvu->getGame()->getTengame(),
                'noidung'=>$nhiemvu->getNoidung(),
                'ngaydang'=>$nhiemvu->getNgaydang(),
                'trangthai'=>$nhiemvu->getTrangthai(),
            );
            return new JsonResponse($jsonData);
        }
    }
}
