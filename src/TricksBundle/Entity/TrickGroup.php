<?php

namespace TricksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrickGroup
 *
 * @ORM\Table(name="trick_group")
 * @ORM\Entity(repositoryClass="TricksBundle\Repository\TrickGroupRepository")
 */
class TrickGroup
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="TricksBundle\Entity\Trick", mappedBy="groups", cascade={"persist","remove"})
     */
    private $tricks;

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
     * @return TrickGroup
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
     * @return TrickGroup
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
     * Constructor
     */
    public function __construct()
    {
        $this->tricks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add trick
     *
     * @param \TricksBundle\Entity\Trick $trick
     *
     * @return TrickGroup
     */
    public function addTrick(\TricksBundle\Entity\Trick $trick)
    {
        $this->tricks[] = $trick;

        return $this;
    }

    /**
     * Remove trick
     *
     * @param \TricksBundle\Entity\Trick $trick
     */
    public function removeTrick(\TricksBundle\Entity\Trick $trick)
    {
        $this->tricks->removeElement($trick);
    }

    /**
     * Get tricks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTricks()
    {
        return $this->tricks;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return TrickGroup
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }
}
