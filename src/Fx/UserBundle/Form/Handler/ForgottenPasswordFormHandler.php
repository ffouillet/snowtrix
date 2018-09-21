<?php

namespace Fx\UserBundle\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Fx\UserBundle\Entity\User;
use Fx\UserBundle\Service\ForgottenPasswordKeyGenerator;
use Fx\UserBundle\Service\Mail\ResetPasswordLinkMailSender;
use Fx\UserBundle\Service\ResetUserPasswordUrlGenerator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ForgottenPasswordFormHandler extends FormHandler {

    /**
     * @var ForgottenPasswordKeyGenerator
     */
    private $forgottenPasswordKeyGenerator;
    /**
     * @var ResetUserPasswordUrlGenerator
     */
    private $resetUserPasswordUrlGenerator;
    /**
     * @var ResetPasswordLinkMailSender
     */
    private $resetPasswordLinkMailSender;

    public function __construct(EntityManagerInterface $em, SessionInterface $session,
                                ForgottenPasswordKeyGenerator $forgottenPasswordKeyGenerator,
                                ResetUserPasswordUrlGenerator $resetUserPasswordUrlGenerator,
                                ResetPasswordLinkMailSender $resetPasswordLinkMailSender)
    {
        parent::__construct($em, $session);

        $this->forgottenPasswordKeyGenerator = $forgottenPasswordKeyGenerator;
        $this->resetUserPasswordUrlGenerator = $resetUserPasswordUrlGenerator;
        $this->resetPasswordLinkMailSender = $resetPasswordLinkMailSender;
    }

    public function handle(Request $request, Form $form)
    {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userEmailOrUsername = $form->getData()['userEmailOrUsername'];

            try {
                $user = $this->em->getRepository('FxUserBundle:User')->findOneByUsernameOrEmail($userEmailOrUsername);
            } catch (\Exception $e) {
                $form->addError(new FormError('Le nom d\'utilisateur ou l\'adresse email que vous avez saisi ne correspondent à aucun utilisateur.'));
                return false;
            }

            // Check if another forgottenPasswordKey have not already been requested.
            $forgottenPasswordKeyCanBeGenerated = $this->forgottenPasswordKeyGenerator->isAbleToGenerateKey($user);

            if ($forgottenPasswordKeyCanBeGenerated['error'] == true) {
                $this->session->getFlashBag()->add(
                    'actionInfoError',
                    $forgottenPasswordKeyCanBeGenerated['errorMessage']
                );

                return false;
            }

            // Generate a key for the user in order for him to reset his password
            // And set an expiry time for this key (now + 1 hour)
            $forgottenPasswordKey = $this->forgottenPasswordKeyGenerator->generateForgottenPasswordKey($user);
            $forgottenPasswordKeyExpiresAt = $this->forgottenPasswordKeyGenerator->generateForgottenPasswordKeyExpirationDateTime();

            $user->setForgottenPasswordKey($forgottenPasswordKey);
            $user->setForgottenPasswordKeyExpiresAt($forgottenPasswordKeyExpiresAt);

            $this->em->flush();

            // Flash message : success
            $this->session->getFlashBag()->add(
                'actionInfoSuccess',
                'Votre demande de nouveau mot de passe à bien été prise en compte, 
                vous recevrez un email concernant les instructions de réinitialisation de votre mot de passe dans quelques instants.'
            );

            $resetPasswordUrl = $this->resetUserPasswordUrlGenerator->generateResetPasswordUrl($user);

            $this->resetPasswordLinkMailSender->sendMail($user, $resetPasswordUrl);

            return true;
        }
    }
}

