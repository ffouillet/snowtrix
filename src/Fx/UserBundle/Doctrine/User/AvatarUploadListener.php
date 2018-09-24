<?php

namespace Fx\UserBundle\Doctrine\User;

use Fx\UserBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Fx\UserBundle\Service\UserAvatarUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AvatarUploadListener implements EventSubscriber
{

    private $userAvatarBaseDir;
    private $defaultAvatarFileName;
    private $userAvatarUploader;

    public function __construct($userAvatarBaseDir, $defaultAvatarFileName, UserAvatarUploader $userAvatarUploader)
    {

        $this->userAvatarBaseDir = $userAvatarBaseDir;
        $this->defaultAvatarFileName = $defaultAvatarFileName;
        $this->userAvatarUploader = $userAvatarUploader;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate','postLoad'];
    }

    public function prePersist(LifecycleEventArgs $args) {
        if ($user = $this->isEntityUser($args)) {

            $this->uploadFile($user);
        }
    }

    public function preUpdate(LifecycleEventArgs $args) {
        if ($user = $this->isEntityUser($args)) {

            $this->uploadFile($user);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        if ($user = $this->isEntityUser($args)) {

            if ($fileName = $user->getAvatar()) {

                $avatarPath = $this->userAvatarUploader->getTargetDirectory($user->getUsername()).'/'.$fileName;
                $avatarWebUrl = $this->userAvatarUploader->getWebDirectory($user->getUsername()).'/'.$fileName;

                if(file_exists($avatarPath)) {
                    $user->setAvatar(new File($avatarPath));
                    $user->setAvatarWebUrl($avatarWebUrl);
                } else {
                    $user->setAvatar(null);
                }
            }
        }
    }

    public function isEntityUser(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return false;
        }

        return $entity;
    }

    public function getUserAvatarBaseDir()
    {
        return $this->userAvatarBaseDir;
    }

    private function uploadFile($user)
    {
        $file = $user->getAvatar();

        // only upload new files
        if ($file instanceof UploadedFile) {

            $fileName = $this->userAvatarUploader->upload($file, $user->getUsername());
            $user->setAvatar($fileName);

            $avatarWebUrl = $this->userAvatarUploader->getWebDirectory($user->getUsername()).'/'.$fileName;
            $user->setAvatarWebUrl($avatarWebUrl);
        } elseif ($file instanceof File) {
            // prevents the full file path being saved on updates
            // as the path is set on the postLoad listener
            $user->setAvatar($file->getFilename());
        }
    }


}
