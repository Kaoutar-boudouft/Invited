<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use App\Service\EmailTokenService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiRegistrationController extends AbstractController
{
    private $manager;
    private $user;
    private $profile;
    private $passwordHasher;
    private $tokenService;

    public function __construct(EntityManagerInterface      $manager, UserRepository $user, ProfileRepository $profile,
                                UserPasswordHasherInterface $passwordHasher,
                                EmailTokenService           $tokenService)
    {
        $this->manager = $manager;
        $this->user = $user;
        $this->profile = $profile;
        $this->passwordHasher = $passwordHasher;
        $this->tokenService = $tokenService;
    }

    #[Route('/api/registration', name: 'app_api_registration', methods: 'POST')]
    public function register(Request $request): JsonResponse{

        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $userName = $data['userName'];
        $password = $data['password'];
        $roles = $data['roles'];

        $emailExists = $this->profile->findOneByEmail($email);
        $userNameExists = $this->user->findOneByUserName($userName);

        if ($emailExists){
            return new JsonResponse([
                'status' => false,
                'message' => 'email already exists ! ',
            ]);
        }

        if ($userNameExists){
            return new JsonResponse([
                'status' => false,
                'message' => 'user name already exists ! ',
            ]);
        }

        else{
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $password
            );

            $profile = new Profile();
            $user->setUserName($userName)->setPassword($hashedPassword)->setRoles($roles['roles']);
            $profile->setUser($user)->setEmail($email);

            $this->manager->persist($user);
            $this->manager->persist($profile);
            $this->manager->flush();

            $token = $this->tokenService
                ->generateToken(['typ' => 'JWT' , 'alg' => 'HS256'], ['user_id' => $user->getId()],
                    $this->getParameter('app.mail_validation_token_secret'));

            $this->tokenService->sendToken($token ,$email);

            return new JsonResponse([
                'status' => true,
                'message' => $token
            ]);

        }
    }

    #[Route('/api/email/validation/{token}', name: 'app_api_email_validation')]
    public function verify($token)
    {
        $token = str_replace('*','.',$token);
        if ($this->tokenService->verifyTokenFormat($token) && $this->tokenService->verifyTokenExp($token) &&
            $this->tokenService->verifyTokenSignature($token, $this->getParameter('app.mail_validation_token_secret')) ){

            $user = $this->tokenService->getTokenUser($token);

            if ($user->getProfileId()->getEmailVerifiedAt()){
                return new JsonResponse([
                    'status' => 201,
                    'message' => 'email already verified ! ',
                ]);
            }
            else{
                $user->getProfileId()->setEmailVerifiedAt(new DateTimeImmutable());
                $this->manager->persist($user);
                $this->manager->flush();

                //return $this->redirect("http://localhost:5173/Login".$token);


                return new JsonResponse([
                    'status' => 200,
                    'message' => 'email verified with success ! ',
                ]);
            }
        }
        else{
            //$token = str_replace(".","*",$token);
            //return $this->redirect("http://localhost:5173/EmailConfirmation/Error/".$token);
            return new JsonResponse([
                'status' => 405,
                'message' => 'token invalid or expired ! ',
                'oldToken' => $token
            ]);
        }



    }

    #[Route('/api/token/regenerate/{oldToken}', name: 'app_api_token_regenerating')]
    public function regenerateToken($oldToken): JsonResponse{
        $oldToken = str_replace('*','.',$oldToken);
        if ($this->tokenService->verifyTokenFormat($oldToken)&& $this->tokenService->verifyTokenSignature($oldToken, $this->getParameter('app.mail_validation_token_secret')) ){
            if ($this->tokenService->getTokenUser($oldToken)->getProfileId()->getEmailVerifiedAt()){
                return new JsonResponse([
                    'status' => 201,
                    'message' => 'email already verified ! ',
                ]);
            }
            else{
                $newToken = $this->tokenService
                    ->generateToken(['typ' => 'JWT' , 'alg' => 'HS256'], ['user_id' => $this->tokenService->getTokenUser($oldToken)->getId()],
                        $this->getParameter('app.mail_validation_token_secret'));
                $this->tokenService->sendToken($newToken,$this->tokenService->getTokenUser($oldToken)->getProfileId()->getEmail());

                return new JsonResponse([
                    'status' => 200,
                    'message' => 'token regenerated and sent successfully ! ',
                ]);
            }
        }
        else{
             return new JsonResponse([
                'status' => 405,
                'message' => 'Regected ! ',
            ]);
        }

    }

    #[Route('/api/admin', name: 'app_api_test')]
    public function test(): JsonResponse{
        return new JsonResponse([
            'status' => 200
        ]);
    }

}
