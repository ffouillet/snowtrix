<?php

namespace ST\TricksBundle\Entity;

use CoreBundle\Entity\Comment;
use Doctrine\ORM\Mapping as ORM;
use ST\TricksBundle\Entity\Trick;

/**
 * TrickComment
 * @ORM\Entity(repositoryClass="ST\TricksBundle\Repository\TrickCommentRepository")
 */
class TrickComment extends Comment
{
    /**
     * @ORM\ManyToOne(targetEntity="ST\TricksBundle\Entity\Trick", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * Set trick
     *
     * @param \ST\TricksBundle\Entity\Trick $trick
     *
     * @return TrickComment
     */
    public function setTrick(Trick $trick)
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * Get trick
     *
     * @return \ST\TricksBundle\Entity\Trick
     */
    public function getTrick()
    {
        return $this->trick;
    }

}
