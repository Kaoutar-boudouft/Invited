<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;

class EmailTokenService
{
    private $userRepository ;
    private $sendMailService;


    public function __construct(UserRepository $userRepository, SendMailService $sendMailService){
        $this->userRepository = $userRepository;
        $this->sendMailService = $sendMailService;
    }

    public function generateToken(
        array $header,
        array $payload,
        string $secret,
        int $validity = 10800
    ) : string
    {

        if ($validity > 0){
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp()+$validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        $base64Header = str_replace(['+','/','='],['-','_',''], $base64Header);
        $base64Payload = str_replace(['+','/','='],['-','_',''], $base64Payload);

        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+','/','='],['-','_',''], $base64Signature);

        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }

    public function sendToken(string $token ,$to){
        $token = str_replace('.',"*",$token);
        $this->sendMailService->send("noreplay@edu.ma", $to, "Account Validation",
            "<p>Click on the link bellow to confirm your email
                         <br><a href='http://localhost:5173/EmailConfirmation/" . $token . "' ><b>CONFIRM !</b></a>
                         </p>");
    }

    public function getTokenPayload(string $token) : array {
        $payload = explode('.', $token);
        return json_decode(base64_decode($payload[1]), true);
    }

    public function getTokenHeader(string $token) : array {
        $header = explode('.', $token);
        return json_decode(base64_decode($header[0]), true);
    }

    public function getTokenUser(string $token) : User{
        $payload = $this->getTokenPayload($token);
        return $this->userRepository->find($payload['user_id']);
    }

    public function verifyTokenFormat(string $token) : bool{
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1 ;
    }

    public function verifyTokenSignature(string $token, string $secret) : bool {
        $header = $this->getTokenHeader($token);
        $payload = $this->getTokenPayload($token);

        $correctToken = $this->generateToken($header, $payload, $secret, 0);

        return $token === $correctToken;
    }

    public function verifyTokenExp(string $token) : bool {
        $payload = $this->getTokenPayload($token);
        $now = new DateTimeImmutable();
        return $payload['exp'] > $now->getTimestamp();
    }


}