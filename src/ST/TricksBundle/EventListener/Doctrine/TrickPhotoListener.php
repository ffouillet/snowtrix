<?php

namespace ST\TricksBundle\EventListener\Doctrine;

use CoreBundle\Service\FileUploader;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ST\TricksBundle\Entity\TrickPhoto;

class TrickPhotoListener implements EventSubscriber
{
    private $uploader;
    private $trickPhotosWebDir;
    private $trickPhotosRootDir;

    public function __construct($trickPhotosWebDir, $trickPhotosRootDir, FileUploader $uploader)
    {
        $this->trickPhotosWebDir = $trickPhotosWebDir;
        $this->uploader = $uploader;
        $this->trickPhotosRootDir = $trickPhotosRootDir;
    }

    public function getSubscribedEvents()
    {
        return ['postPersist', 'postUpdate','postLoad','postRemove'];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $this->uploadFile($entity, $entityManager);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $this->uploadFile($entity, $entityManager);
    }

    private function uploadFile($entity, $entityManager)
    {

        // upload only works for TrickPhoto entities
        if (!$entity instanceof TrickPhoto) {
            return;
        }

        $file = $entity->getPhoto();

        // only upload new files
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file, $entity->getTrick()->getId());

            // I find it a bit ugly, but i'm wondering of to do that more cleanly.
            $entity->setPhoto($fileName);
            $entityManager->flush();

        } elseif ($file instanceof File) {

            // Delete old file, place new one instead

            // prevents the full file path being saved on updates
            // as the path is set on the postLoad listener
            $entity->setPhoto($file->getFilename());

        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof TrickPhoto) {
            return;
        }

        // Inject the full photo web path into the entity property so that rendering can be done easily.
        if ($fileName = $entity->getPhoto()) {
            $photoUrl = $this->trickPhotosWebDir . '/' . $entity->getTrick()->getId() . '/' . $fileName;
            $entity->setPhotoUrl($photoUrl);
        }
    }

    public function postRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!$entity instanceof TrickPhoto) {
            return;
        }

        $currentTrickPhotosRootDir = $this->getCurrentTrickPhotosRootDir($entity);
        $currentTrickPhotoRootPath = $currentTrickPhotosRootDir . $entity->getPhoto();

        // Remove current trick photo
        if (file_exists($currentTrickPhotoRootPath)) {
            unlink($currentTrickPhotoRootPath);
        }


        // If no more file in the trick photo dir, remove the repertory.
        $fileCount = 0;
        $files = glob($currentTrickPhotosRootDir .'*');
        if($files) {
            $fileCount = count($files);
        }

        if($fileCount == 0) {
            if(is_dir($currentTrickPhotosRootDir)) {
                rmdir($currentTrickPhotosRootDir);
            }
        }

    }

    public function getCurrentTrickPhotosRootDir($trickPhoto) {
        $trickPhotosRootDir = $this->trickPhotosRootDir .'/'. $trickPhoto->getTrick()->getId() .'/';

        return $trickPhotosRootDir;
    }
}