<?php

namespace App\Service;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthTokenResult extends AuthenticationSuccessHandler
{

    public function handleAuthenticationSuccess(UserInterface $user, $jwt = null)
    {
       $result = parent::handleAuthenticationSuccess($user, $jwt);
       $resultEncoded = json_decode($result->getContent());

       $payload = explode('.', $resultEncoded->token);
       $payload = json_decode(base64_decode($payload[1]), true);



       return new JsonResponse([
           'token' => $resultEncoded->token,
           'user' => $user->getUserIdentifier(),
           'roles' => $user->getRoles()
        ]);
    }


}