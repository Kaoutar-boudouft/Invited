<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiUserController extends AbstractController
{

    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }


    #[Route('/api/user/{userName}/updatePass', name: 'app_api_user_update', methods: 'PUT')]
    public function UpdateUserPass($userName ,Request $request)
    {
        $user = $this->userRepository->findOneByUserName($userName);
        if ($user) {
            $data = json_decode($request->getContent(), true);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $this->userRepository->upgradePassword($user,$hashedPassword);
        }
    }

//    #[Route('/api/imageUploadTest', name: 'imageUploadTest', methods: 'POST')]
//    public function test(Request $request)
//    {
//
//       return $this->imageUploadService->upload($request->files->get("image") ,'test' );
//            //return $request->files->get("image");
//
//    }

//    #[Route('/api/destroyImage', name: 'imageDestroyTest', methods: 'POST')]
//    public function test1(Request $request)
//    {
//        $data = json_decode($request->getContent(), true);
//
//        return $this->imageUploadService->destroy($data['file'] ,'test' );
//
//    }
}
