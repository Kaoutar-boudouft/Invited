<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    private $tokenService;
    private $userRepository;

    public function __construct(EmailTokenService $emailTokenService, UserRepository $userRepository){
        $this->tokenService = $emailTokenService;
        $this->userRepository = $userRepository;
    }

    public function verifyUserNameWithRequestAuthor($username,$requestHeader) : bool
    {
        $headerAuthorization = $requestHeader["authorization"];
        $token = str_replace('Bearer ','',$headerAuthorization[0]);
        $tokenPayload = $this->tokenService->getTokenPayload($token);

        $user = $this->userRepository->findOneByUserName($tokenPayload['username']);
        $userRoles = $user?$user->getRoles():[];

        return in_array("ROLE_ADMIN", $userRoles) || $tokenPayload['username'] === $username;
    }

}