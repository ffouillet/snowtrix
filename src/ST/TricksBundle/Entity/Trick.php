<?php

namespace ST\TricksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToMany(targetEntity="ST\TricksBundle\Entity\TrickPhoto", mappedBy="trick")
     * @ORM\JoinColumn(nullable=false)
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity="ST\TricksBundle\Entity\TrickVideo", mappedBy="trick")
     * @ORM\JoinColumn(nullable=false)
     */
    private $videos;

    /**
     * @ORM\ManyToMany(targetEntity="ST\TricksBundle\Entity\TrickGroup", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groups;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * @param \ST\TricksBundle\Entity\TrickPhoto $photos
     *
     * @return Trick
     */
    public function setPhotos(\ST\TricksBundle\Entity\TrickPhoto $photos)
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * Get photos
     *
     * @return \ST\TricksBundle\Entity\TrickPhoto
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Add photo
     *
     * @param \ST\TricksBundle\Entity\TrickPhoto $photo
     *
     * @return Trick
     */
    public function addPhoto(\ST\TricksBundle\Entity\TrickPhoto $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \ST\TricksBundle\Entity\TrickPhoto $photo
     */
    public function removePhoto(\ST\TricksBundle\Entity\TrickPhoto $photo)
    {
        $this->photos->removeElement($photo);
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
}
