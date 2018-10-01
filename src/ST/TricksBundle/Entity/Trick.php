<?php

namespace ST\TricksBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trick
 *
 * @ORM\Table(name="trick")
 * @ORM\Entity(repositoryClass="ST\TricksBundle\Repository\TrickRepository")
 */
class Trick
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=2,
     *     minMessage="Le nom de la figure doit contenir au moins 2 caractères.",
     *     max=50,
     *     maxMessage="La description de la figure ne peut pas contenir plus de 50 caractères."
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=3,
     *     minMessage="La description de la figure doit contenir au moins 20 caractères.",
     *     max=1000,
     *     maxMessage="la description de la figure ne peut pas contenir plus de 1000 caractères."
     * )
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="ST\TricksBundle\Entity\TrickPhoto", mappedBy="trick", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity="ST\TricksBundle\Entity\TrickVideo", mappedBy="trick", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $videos;

    /**
     * @ORM\ManyToMany(targetEntity="ST\TricksBundle\Entity\TrickGroup", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="ST\TricksBundle\Entity\TrickComment", mappedBy="trick")
     * @ORM\JoinColumn(nullable=false)
     */
    private $comments;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->photos = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Trick
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Trick
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Trick
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Trick
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Trick
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add group
     *
     * @param \ST\TricksBundle\Entity\TrickGroup $group
     *
     * @return Trick
     */
    public function addGroup(\ST\TricksBundle\Entity\TrickGroup $group)
    {
        $this->groups[] = $group;

        $group->addTrick($this);

        return $this;
    }

    /**
     * Remove group
     *
     * @param \ST\TricksBundle\Entity\TrickGroup $group
     */
    public function removeGroup(\ST\TricksBundle\Entity\TrickGroup $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set photos
     *
     * @param TrickPhoto $photos
     *
     * @return Trick
     */
    public function setPhotos(TrickPhoto $photos)
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * Get photos
     *
     * @return TrickPhoto
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Add video
     *
     * @param \ST\TricksBundle\Entity\TrickVideo $video
     *
     * @return Trick
     */
    public function addVideo(\ST\TricksBundle\Entity\TrickVideo $video)
    {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video
     *
     * @param \ST\TricksBundle\Entity\TrickVideo $video
     */
    public function removeVideo(\ST\TricksBundle\Entity\TrickVideo $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * Add comment.
     *
     * @param \ST\TricksBundle\Entity\TrickComment $comment
     *
     * @return Trick
     */
    public function addComment(\ST\TricksBundle\Entity\TrickComment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param \ST\TricksBundle\Entity\TrickComment $comment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeComment(\ST\TricksBundle\Entity\TrickComment $comment)
    {
        return $this->comments->removeElement($comment);
    }

    /**
     * Get comments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add photo.
     *
     * @param TrickPhoto $photo
     *
     * @return Trick
     */
    public function addPhoto(TrickPhoto $photo)
    {
        $photo->setTrick($this);

        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo.
     *
     * @param TrickPhoto $photo
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePhoto(TrickPhoto $photo)
    {
        return $this->photos->removeElement($photo);
    }
}
