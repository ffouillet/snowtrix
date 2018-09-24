<?php

namespace Fx\UserBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ResetPasswordFormHandler extends FormHandler {

    public function handle(Request $request, Form $form) {
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /*
             * forgottenPasswordKey and forgottenPasswordKeyExpiresAt
             * have been setted to null in User entity, we only have to flush.
             */
            $this->em->flush();

            $this->session->getFlashBag()->add(
                'actionInfoSuccess',
                'Merci ! Votre mot de passe a bien été modifié, vous pouvez dès à présent vous connecter.'
            );

            return true;
        }

        return false;
    }
}