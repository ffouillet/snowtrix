<?php

namespace CoreBundle\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CommentFormHandler
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

    public function handle(Request $request, Form $form, $user) {
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // Set comment's poster
            $comment = $form->getData();
            $comment->setUser($user);

            // Save comment in DB
            $this->em->persist($comment);
            $this->em->flush();

            // Add success flash message
            $this->session->getFlashBag()->add('addComment','Merci ! Votre commentaire a bien été ajouté.');

            return true;
        }

        return false;
    }

}