<?php

namespace Fx\UserBundle\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Fx\UserBundle\Entity\User;
use Fx\UserBundle\Service\UserAvatarUploader;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EditProfileFormHandler extends FormHandler {

    /**
     * @var UserAvatarUploader
     */
    private $userAvatarUploader;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, UserAvatarUploader $userAvatarUploader)
    {
        parent::__construct($em, $session);
        $this->userAvatarUploader = $userAvatarUploader;
    }

    public function handle(Request $request, Form $form) {
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->session->getFlashBag()->add('actionInfoSuccess', 'Merci! Votre profil a bien été modifié.');
            $this->em->flush();

            return true;
        }

        return false;
    }

}