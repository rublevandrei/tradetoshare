<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 */
class Media
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
     * @ORM\Column(type="text", length=255)
     */
    private $name;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @ORM\Column(name="type_id", type="integer")
     */
    private $typeId;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="media")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="media")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $user;

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
     * @return Media
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
     * Set typeId
     *
     * @param integer $typeId
     *
     * @return Media
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
      //  $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return Media
     */
    public function setPost(\AppBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \AppBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Media
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
