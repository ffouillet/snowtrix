<?php

namespace Fx\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Fx\UserBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email déjà utilisé. Merci de choisir une autre adresse email.")
 * @UniqueEntity(fields="username", message="Nom d'utilisateur déjà utilisé. Merci de choisir un autre nom d'utilisateur.")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9]+$/",
     *     message="Votre nom d'utilisateur ne peut contenir que des lettres non accentuées, des chiffres ou les caractères suivants : -_."
     * )
     * @Assert\Length(
     *     min=3,
     *     minMessage="Votre nom d'utilisateur doit contenir au moins 3 caractères.",
     *     max=25,
     *     maxMessage="Votre nom d'utilisateur ne peut pas contenir plus de 25 caractères"
     * )
     *
     */
    private $username;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(min=8, max=4096, minMessage="Votre mot de passe doit contenir au moins 8 caractères.")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $forgottenPasswordKey;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $forgottenPasswordKeyExpiresAt;

    /**
     * @ORM\Column(type="string", length=254, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * Only used for the FileUpload of the UserEditType
     * Avatar will always be stored the same way.
     * Check the method : getAvatarRootPath()
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     *
     * @Assert\Image(
     *     mimeTypes={"image/jpeg", "image/png"},
     *     mimeTypesMessage="Format d'image incorrect, merci d'utiliser les formats d'images suivants : .png, .jpg",
     *     minWidth=75, minWidthMessage="La largeur de votre photo de profil ne peut être inférieure à 75px",
     *     maxWidth=150, maxWidthMessage="La largeur de votre photo de profil ne peut être supérieure à 150px",
     *     minHeight=75, minHeightMessage="La hauteur de votre photo de profil ne peut être inférieure à 75px",
     *     maxHeight=150, maxHeightMessage="La hauteur de votre photo de profil ne peut être supérieure à 150px")
     */
    private $avatarFileName;

    /**
     * Is fulfilled by Fx\UserBundle\Doctrine\User\AvatarUrlListener;
     */
    private $avatarWebUrl;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;


    public function __construct()
    {
        $this->isActive = true;
        $this->roles = array('ROLE_USER');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function __toString() {
        return $this->username;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set plainPassword
     *
     * @param string $password
     *
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        /*
         * We need to add this line because Doctrine listeners are not called
         * if Doctrine thinks that an object has not been updated.
         * A listener will be called in order to encode the password
         */
        $this->password = null;
        $this->forgottenPasswordKey = null;
        $this->forgottenPasswordKeyExpiresAt = null;

        return $this;
    }

    /**
     * Set forgottenPasswordKey
     *
     * @param string $forgottenPasswordKey
     *
     * @return User
     */
    public function setForgottenPasswordKey($forgottenPasswordKey)
    {
        $this->forgottenPasswordKey = $forgottenPasswordKey;

        return $this;
    }

    /**
     * Get forgottenPasswordKey
     *
     * @return string
     */
    public function getForgottenPasswordKey()
    {
        return $this->forgottenPasswordKey;
    }

    /**
     * Set forgottenPasswordKeyExpiresAt
     *
     * @param \DateTime $forgottenPasswordKeyExpiresAt
     *
     * @return User
     */
    public function setForgottenPasswordKeyExpiresAt($forgottenPasswordKeyExpiresAt)
    {
        $this->forgottenPasswordKeyExpiresAt = $forgottenPasswordKeyExpiresAt;

        return $this;
    }

    /**
     * Get forgottenPasswordKeyExpiresAt
     *
     * @return \DateTime
     */
    public function getForgottenPasswordKeyExpiresAt()
    {
        return $this->forgottenPasswordKeyExpiresAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
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
     * Set avatarFileName
     *
     * @param string $avatarFileName
     *
     * @return User
     */
    public function setAvatarFileName($avatarFileName)
    {
        $this->avatarFileName = $avatarFileName;

        return $this;
    }

    /**
     * Get avatarFileName
     *
     * @return string
     */
    public function getAvatarFileName()
    {
        return $this->avatarFileName;
    }

    /**
     * Set avatarWebUrl
     *
     * @param string $avatarWebUrl
     *
     * @return User
     */
    public function setAvatarWebUrl($avatarWebUrl)
    {
        $this->avatarWebUrl = $avatarWebUrl;

        return $this;
    }

    /**
     * Get avatarWebUrl
     *
     * @return string
     */
    public function getAvatarWebUrl()
    {
        return $this->avatarWebUrl;
    }
}
