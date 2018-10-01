<?php

namespace ST\TricksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Image(
     *     mimeTypes={"image/jpeg", "image/png"},
     *     mimeTypesMessage="Format de photo incorrect, merci d'utiliser les formats d'images suivants : .png, .jpg",
     *     minWidth=150, minWidthMessage="La largeur d'une photo ne peut être inférieure à 150px.",
     *     maxWidth=1920, maxWidthMessage="La largeur d'une photo ne peut être supérieure à 1920px.",
     *     minHeight=150, minHeightMessage="La hauteur d'une photo ne peut être inférieure à 150px.",
     *     maxHeight=1080, maxHeightMessage="La hauteur d'une photo ne peut être supérieure à 1080px.")
     */
    private $photo;

    /**
     * Used to render the photo on front
     */
    private $photoUrl;

    /**
     * Used for the upload process in order to set photo's correct name during Doctrine postPersist Event.
     */
    private $photoGeneratedFileName;

    /**
     * @ORM\ManyToOne(targetEntity="ST\TricksBundle\Entity\Trick", inversedBy="photos")
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
     * Get photo url
     *
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * Set photo url
     *
     * @param string $photo
     *
     * @return TrickPhoto
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    /**
     * Get photo generated file name
     *
     * @return string
     */
    public function getPhotoGeneratedFileName()
    {
        return $this->photoGeneratedFileName;
    }

    /**
     * Set photo generated file name
     *
     * @param string $photoGeneratedFileName
     *
     * @return TrickPhoto
     */
    public function setPhotoGeneratedFileName($photoGeneratedFileName)
    {
        $this->photoGeneratedFileName = $photoGeneratedFileName;

        return $this;
    }

    /**
     * Set trick
     *
     * @param Trick $trick
     *
     * @return TrickPhoto
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
