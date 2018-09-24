<?php

namespace ST\TricksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrickPhoto
 *
 * @ORM\Table(name="trick_photo")
 * @ORM\Entity(repositoryClass="ST\TricksBundle\Repository\TrickPhotoRepository")
 */
class TrickPhoto
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
     * @ORM\Column(name="photo", type="string", length=255, unique=true)
     */
    private $photo;

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
     * Set photo
     *
     * @param string $photo
     *
     * @return TrickPhoto
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set trick
     *
     * @param \ST\TricksBundle\Entity\Trick $trick
     *
     * @return TrickPhoto
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
