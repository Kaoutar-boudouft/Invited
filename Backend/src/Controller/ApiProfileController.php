<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiProfileController extends AbstractController
{
    private UserRepository $userRepository;
    private ProfileRepository $profileRepository;

    /**
     * @param UserRepository $userRepository
     * @param ProfileRepository $profileRepository
     */
    public function __construct(UserRepository $userRepository, ProfileRepository $profileRepository)
    {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }


    #[Route('/api/profile/{userName}', name: 'app_api_profile', methods: 'GET')]
    public function getUserByUsername($userName)
    {
        $user = $this->userRepository->findOneByUserName($userName);


        if ($user) {
            $profile = $this->profileRepository->getUserProfileById($user->getProfileId()->getId()) ;
            return new JsonResponse([
                "profile" => $profile[0]
            ]);
        }

    }

    #[Route('/api/Profile/{userName}/update', name: 'app_api_profile_update', methods: 'PUT')]
    public function UpdateProfile($userName ,Request $request)
    {
        $user = $this->userRepository->findOneByUserName($userName);
        if ($user) {

            $query = $this->profileRepository->UpdateProfile($request,$user->getProfileId()->getId());
            return $this->json([
                'status' => $query === 1 ? 200 : 201,
                'message' => $query === 1 ? "profile updated successfully ! " : "profile doesn't updated !"
            ]);
        }
    }

    #[Route('/api/Profile/{userName}/updateProfilePic', name: 'app_api_profilePic_update', methods: 'PUT')]
    public function UpdateProfilePic($userName ,Request $request)
    {
        $user = $this->userRepository->findOneByUserName($userName);
        if ($user) {

            $query = $this->profileRepository->UpdateProfilePic($request,$user->getProfileId()->getId());
            return $this->json([
                'status' => $query === 1 ? 200 : 201,
                'message' => $query === 1 ? "profile picture updated successfully ! " : "profile picture doesn't updated !"
            ]);
        }
    }
}
