<?php

namespace Fx\UserBundle\Doctrine\User;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Fx\UserBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AvatarUrlListener implements EventSubscriber
{

    private $userAvatarBaseDir;
    private $defaultAvatarFileName;

    public function __construct($userAvatarBaseDir, $defaultAvatarFileName)
    {

        $this->userAvatarBaseDir = $userAvatarBaseDir;
        $this->defaultAvatarFileName = $defaultAvatarFileName;
    }

    public function getSubscribedEvents()
    {
        return ['postLoad', 'postUpdate'];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        if ($user = $this->isEntityUser($args)) {
            dump('postLoad');
            dump($this->getAvatarRootUrl($user));
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($user = $this->isEntityUser($args)) {
            dump('postFlush');
            dump($this->getAvatarRootUrl($user));
        }
    }

    public function isEntityUser(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return false;
        }

        return $entity;
    }

    public function deleteUserAvatar()
    {
        if ($userAvatarRootUrl = $this->getAvatarRootUrl() != null) {
            unlink($userAvatarRootUrl);
        }
    }

    public function getAvatarRootUrl(User $user)
    {
        // Check if user have already uploaded an avatar
        if($user->getAvatarFileName() != null) {
            $userAvatarUrl =
                $this->getUserAvatarBaseDir() . '/' . $user->getUsername() . '/' . $user->getAvatarFileName();
        } else {
            return null;
        }
    }

    public function getDefaultAvatarRootUrl()
    {
        $userDefaultAvatarUrl = $this->getUserAvatarBaseDir() . '/' . $this->defaultAvatarFileName;

        return $userDefaultAvatarUrl;
    }

    public function getUserAvatarBaseDir()
    {
        return $this->userAvatarBaseDir;
    }


}
