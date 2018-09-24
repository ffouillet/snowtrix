<?php

namespace Fx\UserBundle\Service\Mail;

use Fx\UserBundle\Entity\User;

class ResetPasswordLinkMailSender {

    private $mailer;
    private $twig;
    private $mailSenderEmailAddress;

    public function __construct(\Swift_Mailer $mailer, \Twig\Environment $twig, $mailSenderEmailAddress)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailSenderEmailAddress = $mailSenderEmailAddress;
    }

    public function sendMail(User $user, $resetPasswordUrl)
    {

        $message = (new \Swift_Message('SnowTrix - Demande de réinitialisation de mot de passe.'))
            ->setFrom($this->mailSenderEmailAddress)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'fx/security/mail/reset_password.html.twig',
                    array('user' => $user,
                        'resetPasswordUrl' => $resetPasswordUrl)
                )
            )
            ->setContentType('text/html')
        ;

        $this->mailer->send($message);

    }
}