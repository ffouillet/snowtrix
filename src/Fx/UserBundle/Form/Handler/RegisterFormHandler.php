<?php

namespace Fx\UserBundle\Form\Handler;

use Fx\UserBundle\Entity\User;
use Symfony\Component\Form\Form;

class RegisterFormHandler extends FormHandler {

    public function handle(Request $request, Form $form, User $user) {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*
            * Password encoding is done with Doctrine Event Listener :
            * Fx\UserBundle\Security\HashPasswordListener
            */
            $this->em->persist($user);
            $this->em->flush();

            // Flash message
            $this->session->getFlashBag()->add(
                'actionInfoSuccess',
                'Merci ! Votre inscription a bien été prise en compte. Vous pouvez dès à présent vous connecter.'
            );

            return true;
        }

        return false;
    }
}