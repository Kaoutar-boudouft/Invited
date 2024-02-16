<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class FailedInvitSendSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            WorkerMessageFailedEvent::class => 'onSendFailed'
        ];
    }

    public function onSendFailed(WorkerMessageFailedEvent $event){
        dd($event->getEnvelope());
    }
}