<?php

namespace ST\TricksBundle\EventListener\Doctrine;

use CoreBundle\Service\FileUploader;
use CoreBundle\Service\FxStringsTools;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ST\TricksBundle\Entity\Trick;

class TrickListener implements EventSubscriber
{

    private $fxStringsTools;
    private $trickPhotosWebDir;

    public function __construct($trickPhotosWebDir, FxStringsTools $fxStringsTools)
    {
        $this->fxStringsTools = $fxStringsTools;
        $this->trickPhotosWebDir = $trickPhotosWebDir;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist','preUpdate'];
    }

    // Set the Trick's slug
    public function prePersist(LifecycleEventArgs $args)
    {
        $trick = $args->getEntity();

        $this->updateSlug($trick);

    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $trick = $args->getEntity();

        $this->updateSlug($trick);

    }

    public function updateSlug($trick) {

        if (!$trick instanceof Trick) {
            return;
        }

        $slug = $this->fxStringsTools->quickSlugify($trick->getName());

        $trick->setSlug($slug);
    }
}