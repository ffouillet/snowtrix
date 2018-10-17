<?php

namespace ST\TricksBundle\Form\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TrickEditFormHandler extends FormHandler
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

        $currentTrick = $form->getData();

        // In order to update trick photos and handle deleted trick photos
        // Create an ArrayCollection of the current Tag objects in the database before form handleRequest
        $originalTrickPhotos = new ArrayCollection();

        foreach($currentTrick->getPhotos() as $originalPhoto) {
            $originalTrickPhotos->add($originalPhoto);
        }

        // Same for trick videos
        $originalTrickVideos = new ArrayCollection();

        foreach($currentTrick->getVideos() as $originalVideo) {
            $originalTrickVideos->add($originalVideo);
        }

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $currentTrick = $form->getData(); // Just for readability.

            // Handle new trick photos, updated and deleted ones
            $this->handleTrickPhotosUpdate($currentTrick, $originalTrickPhotos);

            // Same for videos
            $this->handleTrickVideosUpdate($currentTrick, $originalTrickVideos);

            $this->em->flush();

            $this->session->getFlashBag()->add(
                'actionInfoSuccess',
                'Merci ! La figure "'.$currentTrick->getName().'" a été modifiée avec succès.'
            );

            return true;
        }

        return false;
    }

    // Handle update and deletions of trick photos
    private function handleTrickPhotosUpdate($currentTrick, $originalTrickPhotos) {

        // Remove deleted trick photos during form submission
        foreach($originalTrickPhotos as $originalPhoto) {

            if (false === $currentTrick->getPhotos()->contains($originalPhoto)) {
                $currentTrick->getPhotos()->removeElement($originalPhoto);
            } else {
                /*
                 * If photo didn't changed, we replace it with the same but from the DB because TrickPhoto's photo attribute has
                 * been set to null with form->handleRequest() (if nothing has been submitted via the input field).
                 * Like that, Doctrine won't see an update and won't throw an error indicating that a new relation has been found.
                 */
                if(!$originalPhoto->getPhoto() instanceof UploadedFile && $originalPhoto->getPhotoUrl() != '') {
                    foreach($currentTrick->getPhotos() as $photoKey => $photoValue) {
                        if($photoValue->getId() == $originalPhoto->getId()) {
                            // Simply refresh the entity from DB.
                            $this->em->refresh($photoValue);
                        }
                    }
                }
            }
        }

        // Add new tricks photos if there are
        foreach($currentTrick->getPhotos() as $photo) {
            // Persist photo if it's a new one
            if($photo->getId() == null && $photo->getPhoto() instanceof UploadedFile) {
                $photo->setTrick($currentTrick);
                $this->em->persist($photo);
            }
        }

    }

    private function handleTrickVideosUpdate($currentTrick, $originalTrickVideos) {

        foreach($originalTrickVideos as $originalVideo) {
            if (false === $currentTrick->getVideos()->contains($originalVideo)) {
                $currentTrick->removeVideo($originalVideo);
                // No need to remove manually because Trick->videos relation have orphanRemoval set to true
            }
        }

        // Add new videos if there are
        foreach($currentTrick->getVideos() as $video) {
            if($video->getId() == null) {
                $video->setTrick($currentTrick);
                // No need to persist manually, cascade persist defined in Trick video relation.
            }
        }
    }
}