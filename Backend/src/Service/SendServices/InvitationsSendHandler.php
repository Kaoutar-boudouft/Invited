<?php

namespace App\Service\SendServices;

use App\Entity\User;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InvitationsSendHandler
{
    private EntityManagerInterface $entityManager;
    private SendMailService $sendMailService;

    public function __construct(
        EntityManagerInterface $entityManager ,
        SendMailService $sendMailService
    ){
        $this->entityManager = $entityManager;
        $this->sendMailService = $sendMailService;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(InvitationsSendService $invitationsSendService): void
    {
        $this->sendMailService->send("","","Queue Test","<h1>Done !</h1>");

    }
}