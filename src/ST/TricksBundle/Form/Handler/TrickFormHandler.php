<?php

namespace ST\TricksBundle\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TrickFormHandler extends FormHandler
{

    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var Session
     */
    protected $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function handle(Request $request, Form $form) {

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $trick = $form->getData();

            // Association between trick and photos, trick and videos.
            // I guess if there is a way to do that automatically...
            foreach($trick->getPhotos() as $photo) {
                // Persist it manually cause no cascade persist has been set on Trick's photos relation because of photo update and deletion.
                $this->em->persist($photo);
                $photo->setTrick($trick);
            }

            foreach($trick->getVideos() as $video) {
                $this->em->persist($video);
                $video->setTrick($trick);
            }

            $this->em->persist($form->getData());

            $this->em->flush();

            $this->session->getFlashBag()->add(
                'actionInfoSuccess',
                'Merci ! Votre figure "'.$trick->getName().'" a été ajoutée avec succès .'
            );

            return true;
        }

        return false;
    }
}