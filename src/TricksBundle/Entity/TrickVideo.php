<?php

namespace TricksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TrickVideo
 *
 * @ORM\Table(name="trick_video")
 * @ORM\Entity(repositoryClass="TricksBundle\Repository\TrickVideoRepository")
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
     * @ORM\Column(name="embedCode", type="string", length=255)
     * @Assert\NotBlank(message="Le code embed de la vidéo ne peut être vide.")
     * @Assert\Regex(
     *     pattern="/^<iframe.*><\/iframe>$/",
     *     message="Format de code embed de la vidéo incorrect. Merci de vous réferer à l'aide en cas de besoin."
     * )
     */
    private $embedCode;

    /**
     * @ORM\ManyToOne(targetEntity="TricksBundle\Entity\Trick", inversedBy="videos")
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
     * @param Trick $trick
     *
     * @return TrickVideo
     */
    public function setTrick(Trick $trick)
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * Get trick
     *
     * @return Trick
     */
    public function getTrick()
    {
        return $this->trick;
    }
}
