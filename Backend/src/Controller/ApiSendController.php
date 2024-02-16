<?php

namespace App\Controller;

use App\Service\SendServices\InvitationsSendService;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiSendController extends AbstractController
{
    #[Route('/api/send', name: 'app_api_send')]
    public function index(MessageBusInterface $messageBus)
    {
        $messageBus->dispatch(new InvitationsSendService(2));
        return "done !";
    }
}
