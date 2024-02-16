<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiContactController extends AbstractController
{
    private $contactRepository;
    private $userRepository;
    private $userService;
    private $manager;

    public function __construct(UserRepository $userRepository ,EntityManagerInterface $manager ,
                                ContactRepository $contactRepository ,UserService $userService ){
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->contactRepository = $contactRepository;
        $this->userService = $userService;
    }

    #[Route('/api/user/{username}/contacts', name: 'app_api_user_contacts', methods: 'GET')]
    public function getUserContacts(Request $request,$username) : JsonResponse
    {
        if($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())){
            $user = $this->userRepository->findOneByUserName($username);
            if ($user){

                $data = $this->contactRepository->getUserContacts($user);
                return new JsonResponse([
                    'status' => 200,
                    'contacts' => $data
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

    #[Route('/api/user/{username}/contacts/{contactId}/view', name: 'app_api_user_contact', methods: 'GET')]
    public function getUserContactById(Request $request, $username, $contactId): JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())){
            $user = $this->userRepository->findOneByUserName($username);
            if ($user){

                $result = $this->contactRepository->getUserContactById($user,$contactId);
                if (count($result)>0){
                    return new JsonResponse([
                        'status' => 200,
                        'contact' => $result
                    ]);
                }
                else{
                    return new JsonResponse([
                        'status' => 201,
                        'message' => "contact doesn't exist!"
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

    #[Route('/api/user/{username}/contacts/{contactId}/delete', name: 'app_api_user_delete_contact', methods: 'delete')]
    public function deleteUserContactById(Request $request, $username, $contactId) : JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())){
            $user = $this->userRepository->findOneByUserName($username);
            if ($user){

                $data = $this->contactRepository->deleteUserContactById($user,$contactId);
                $contacts = $this->contactRepository->getUserContacts($user);
                return new JsonResponse([
                    'status' => 200,
                    'message' => $data ===1 ?? "contact deleted successfully !",
                    'contacts' => $contacts
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

    #[Route('/api/user/{username}/contacts/new', name: 'app_api_user_new_contact', methods: 'POST')]
    public function addNewContact($username ,Request $request): JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())) {
            $user = $this->userRepository->findOneByUserName($username);
            if ($user) {

                $contact = $this->contactRepository->addNewContact($request,$user);
                return $this->json([
                    'status' => 200,
                    'message' => 'contact added successfully !',
                    'contactId' => $contact->getId()
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

    #[Route('/api/user/{username}/contacts/{contactId}/update', name: 'app_api_user_update_contact', methods: 'PUT')]
    public function UpdateContact($username ,$contactId ,Request $request): JsonResponse
    {
        if ($this->userService->verifyUserNameWithRequestAuthor($username, $request->headers->all())) {
            $user = $this->userRepository->findOneByUserName($username);
            if ($user) {

                $query = $this->contactRepository->UpdateContact($request,$contactId);
                return $this->json([
                    'status' => 200,
                    'message' => $query === 1 ? "contact updated successfully ! " : "contact doesn't updated !"
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
