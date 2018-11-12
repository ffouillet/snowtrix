<?php

namespace UserBundle\Service;

use UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetUserPasswordUrlGenerator {

    const RESET_PASSWORD_ROUTE = 'fx_reset_password';

    private $router;

    public function __construct(UrlGeneratorInterface $router){

        $this->router = $router;
    }

    public function generateResetPasswordUrl(User $user) {

        $userForgottenPasswordKey = $user->getForgottenPasswordKey();

        if($userForgottenPasswordKey == "") {
            throw new \RuntimeException("Impossible de générer une URL pour réinitialiser votre mot de passe, clef de reset password vide.");
        }

        $userId = $user->getId();

        $resetPasswordUrl = $this->router->generate(self::RESET_PASSWORD_ROUTE,
            array('forgottenPasswordKey' => $userForgottenPasswordKey,
                'userId' => $userId),
            UrlGeneratorInterface::ABSOLUTE_URL);


        return $resetPasswordUrl;

    }
}