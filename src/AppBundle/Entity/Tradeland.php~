<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Tradeland
 *
 * @ORM\Table(name="tradeland")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TradelandRepository")
 */
class Tradeland
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
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=2000)
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=2000)
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false, options={"default" : 0})
     */
    private $blocked = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="tradelands")
     */
    private $users;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownTradelands")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $owner;

    /**
     *
     * @ORM\OneToMany(targetEntity="Post", mappedBy="tradeland", cascade={"persist"})
     */
    private $posts;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->posts = new ArrayCollection();
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
     * @return Tradeland
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
     * Set summary
     *
     * @param string $summary
     *
     * @return Tradeland
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tradeland
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
     * Set blocked
     *
     * @param boolean $blocked
     *
     * @return Tradeland
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * Get blocked
     *
     * @return boolean
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Tradeland
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     *
     * @return Tradeland
     */
    public function setOwner(\AppBundle\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return Tradeland
     */
    public function addPost(\AppBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \AppBundle\Entity\Post $post
     */
    public function removePost(\AppBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Tradeland
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
}
