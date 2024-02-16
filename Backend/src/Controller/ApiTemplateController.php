<?php

namespace App\Controller;

use App\Entity\Template;
use App\Repository\TemplateRepository;
use App\Repository\UserRepository;
use App\Service\EmailTokenService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiTemplateController extends AbstractController
{
    private $templateRepository;
    private $userRepository;
    private $userService;

    public function __construct(UserRepository $userRepository ,TemplateRepository $templateRepository ,UserService $userService ){
        $this->userRepository = $userRepository;
        $this->templateRepository = $templateRepository;
        $this->userService = $userService;
    }

    #[Route('/api/user/{username}/templates', name: 'app_api_user_templates', methods: 'GET')]
    public function getUserTemplates(Request $request,$username) : JsonResponse
    {
        if($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())){
            $user = $this->userRepository->findOneByUserName($username);
            if ($user){
                $data = $this->templateRepository->getUserTemplates($user);
                return new JsonResponse([
                    'status' => 200,
                    'templates' => $data
                ]);
            }
            return new JsonResponse([
                'status' => 201,
                'message' => "user with the name ".$username." not found !"
            ]);
        }
        else{
            return new JsonResponse([
                'status' => 201,
                'message' => "you haven't permission to view that !"
            ]);
        }

    }

    #[Route('/api/user/{username}/templates/{tempId}/view', name: 'app_api_user_template', methods: 'GET')]
    public function getUserTemplateById(Request $request, $username, $tempId): JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())){
            $user = $this->userRepository->findOneByUserName($username);
            if ($user){
                $data = $this->templateRepository->getUserTemplateById($user,$tempId);
                if (count($data)>0){
                    return new JsonResponse([
                        'status' => 200,
                        'template' => $data
                    ]);
                }
                else{
                    return new JsonResponse([
                        'status' => 201,
                        'template' => "template doesn't exist!"
                    ]);
                }
            }
            else{
                return new JsonResponse([
                    'status' => 201,
                    'message' => "user with the name ".$username." not found !"
                ]);
            }
        }
        else{
                return new JsonResponse([
                    'status' => 201,
                    'message' => "you haven't permission to view that !"
                ]);
        }
    }

    #[Route('/api/user/{username}/templates/{tempId}/delete', name: 'app_api_user_delete_template', methods: 'delete')]
    public function deleteUserTemplateById(Request $request, $username, $tempId) : JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())){
            $user = $this->userRepository->findOneByUserName($username);
            if ($user){

                $query = $this->templateRepository->deleteUserTemplateById($user,$tempId);
                $templates = $this->templateRepository->getUserTemplates($user);

                return new JsonResponse([
                    'status' => 200,
                    'message' => $query ===1 ?? "template deleted successfully !",
                    'templates' => $templates
                ]);
            }
            else{
                return new JsonResponse([
                    'status' => 201,
                    'message' => "user with the name ".$username." not found !"
                ]);
            }
        }
        else{
            return new JsonResponse([
                'status' => 201,
                'message' => "you haven't permission to delete that !"
            ]);
        }
    }

    #[Route('/api/user/{username}/templates/new', name: 'app_api_user_new_template', methods: 'POST')]
    public function addNewTemplate($username ,Request $request): JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())) {
            $user = $this->userRepository->findOneByUserName($username);
            if ($user) {
                $template = $this->templateRepository->addNewTemplate($request,$user);
                return $this->json([
                    'status' => 200,
                    'msg' => 'template added successfully !',
                    'tempId' => $template->getId()
                ]);
            }
            else {
                return new JsonResponse([
                    'status' => 201,
                    'message' => "user with the name " . $username . " not found !"
                ]);
            }
        }
        else {
            return new JsonResponse([
                'status' => 201,
                'message' => "you haven't permission to create that !"
            ]);
        }
    }

    #[Route('/api/user/{username}/templates/{tempId}/update', name: 'app_api_user_update_template', methods: 'PUT')]
    public function UpdateTemplate($username ,$tempId ,Request $request): JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())) {
            $user = $this->userRepository->findOneByUserName($username);
            if ($user) {

                $query = $this->templateRepository->UpdateTemplate($request,$tempId);
                return $this->json([
                    'status' => 200,
                    'msg' => $query === 1 ? "template updated successfully ! " : "template doesn't updated !"
                ]);
            }
            else {
                return new JsonResponse([
                    'status' => 201,
                    'message' => "user with the name " . $username . " not found !"
                ]);
            }
        }
        else {
            return new JsonResponse([
                'status' => 201,
                'message' => "you haven't permission to create that !"
            ]);
        }
    }

}
