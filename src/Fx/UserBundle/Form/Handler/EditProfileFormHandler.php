<?php

namespace Fx\UserBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class EditProfileFormHandler extends FormHandler {

    public function handle(Request $request, Form $form, $user) {
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // If password has been changed,
            dump($user);

            return true;
        }

        return false;
    }
}