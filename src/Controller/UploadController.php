<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;

class UploadController extends AbstractController
{
    /**
     * @Route("/doUpload", name="do-uploads")
     * @param Request $request
     * @param string $uploadDir
     * @param FileUploader $uploader
     * @param LoggerInterface $logger
     * @param Session $session
     * @return Response
     */
    public function index(Request $request, string $uploadDir,
                          FileUploader $uploader, LoggerInterface $logger, Session $session): Response
    {
        $token = $request->get("token");

        if (!$this->isCsrfTokenValid('uploads', $token))
        {
            $logger->info("CSRF failure");

            return new Response("Operation not allowed",  Response::HTTP_BAD_REQUEST,
                ['content-type' => 'text/plain']);
        }

        $file = $request->files->get('myfile');

        if (empty($file))
        {
            return new Response("No file specified",
                Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
        }
        else {
            $allowed = array("jpg" => "images/jpg", "jpeg" => "images/jpeg", "gif" => "images/gif", "png" => "images/png");
            // Xác minh phần mở rộng tệp
            for ($i=0;$i<count($file);$i++) {
                $filename = $file[$i]->getClientOriginalName();
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                dd($ext);
                if(!array_key_exists($ext, $allowed)) {
                    $session->set('loifile',"Lỗi: Định dạng file ảnh không hợp lệ!");
                    return $this->redirectToRoute("add_taikhoangame");
                }
            }
            for ($i=0;$i<count($file);$i++) {
                $filename = $file[$i]->getClientOriginalName();
                $uploader->upload($uploadDir, $file[$i], $filename);
            }
            return $this->redirectToRoute("test");
        }

        return $this->redirectToRoute("quanly_taikhoangame");
    }
}