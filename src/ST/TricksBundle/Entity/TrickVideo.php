<?php

namespace ST\TricksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrickVideo
 *
 * @ORM\Table(name="trick_video")
 * @ORM\Entity(repositoryClass="ST\TricksBundle\Repository\TrickVideoRepository")
 */
class TrickVideo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="embedCode", type="string", length=255, unique=true)
     */
    private $embedCode;

    /**
     * @ORM\ManyToOne(targetEntity="ST\TricksBundle\Entity\Trick", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set embedCode
     *
     * @param string $embedCode
     *
     * @return TrickVideo
     */
    public function setEmbedCode($embedCode)
    {
        $this->embedCode = $embedCode;

        return $this;
    }

    /**
     * Get embedCode
     *
     * @return string
     */
    public function getEmbedCode()
    {
        return $this->embedCode;
    }

    /**
     * Set trick
     *
     * @param \ST\TricksBundle\Entity\Trick $trick
     *
     * @return TrickVideo
     */
    public function setTrick(\ST\TricksBundle\Entity\Trick $trick)
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
