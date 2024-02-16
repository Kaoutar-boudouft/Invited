<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendMailService
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function
    send(
        string $from,
        string $to,
        string $subject,
        string $template,
    ){
        $email = (new Email())
            ->from($from)
            ->to($to)
            //->cc ('cc@example.com')
            //->bcc ('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority (Email:: PRIORITY_HIGH)
            ->subject($subject)
            ->html( $template);
        sleep(2);
        $this->mailer->send($email);
    }

}