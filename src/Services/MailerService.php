<?php

namespace App\Services;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{

    public function __construct(private readonly MailerInterface $mailer) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail($text): void

    {
        $email = (new Email())
            ->to('youssefch123@example.com')
            ->from('youssef@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Hello, Symfony Mailer!')
            ->text($text)
            ->html('<p>See Twig integration for better HTML integration!</p>');
        $this->mailer->send($email);
    }

}