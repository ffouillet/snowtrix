<?php

namespace ST\TricksBundle\EventListener\Doctrine;

use CoreBundle\Service\FileUploader;
use CoreBundle\Service\FxStringsTools;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ST\TricksBundle\Entity\Trick;
use ST\TricksBundle\Entity\TrickPhoto;

class TrickListener implements EventSubscriber
{

    private $fxStringsTools;

    public function __construct(FxStringsTools $fxStringsTools)
    {
        $this->fxStringsTools = $fxStringsTools;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist'];
    }

    // Set the Trick's slug
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Trick) {
            return;
        }

        $slug = $this->fxStringsTools->quickSlugify($entity->getName());

        $entity->setSlug($slug);

        dump($entity);
    }

}