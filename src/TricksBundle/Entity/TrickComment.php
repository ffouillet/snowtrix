<?php

namespace TricksBundle\Entity;

use CoreBundle\Entity\Comment;
use Doctrine\ORM\Mapping as ORM;
use TricksBundle\Entity\Trick;

/**
 * TrickComment
 * @ORM\Entity(repositoryClass="TricksBundle\Repository\TrickCommentRepository")
 */
class TrickComment extends Comment
{
    /**
     * @ORM\ManyToOne(targetEntity="TricksBundle\Entity\Trick", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * Set trick
     *
     * @param \TricksBundle\Entity\Trick $trick
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
     * @return \TricksBundle\Entity\Trick
     */
    public function getTrick()
    {
        return $this->trick;
    }

}
