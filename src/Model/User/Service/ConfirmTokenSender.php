<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Twig\Environment;

class ConfirmTokenSender
{
    /**
     * @var \Swift_Mailer
     */
    private \Swift_Mailer $mailer;
    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var array
     */
    private array $from;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, array $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    public function send(Email $email, string $token): void
    {
        $message = (new \Swift_Message('Sign Up Confirmation'))
            ->setFrom($this->from)
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/signup.html.twig', [
                'token' => $token
            ]), 'text/html');

        if (!$this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send message');
        }
    }
}