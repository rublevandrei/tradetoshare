<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotificationRepository")
 */
class Notification
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="json_array")
     */
    private $data;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $readAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->updatedTimestamps();
    }
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime("now"));

        if($this->getCreatedAt() == null)
        {
            $this->setCreatedAt(new \DateTime("now"));
        }
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Notification
     */
    public function setUser(\AppBundle\Entity\User $user)
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

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set readAt
     *
     * @param \DateTime $readAt
     *
     * @return Notification
     */
    public function setReadAt($readAt)
    {
        $this->readAt = $readAt;

        return $this;
    }

    /**
     * Get readAt
     *
     * @return \DateTime
     */
    public function getReadAt()
    {
        return $this->readAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Notification
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
     * @return Notification
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
     * Set data
     *
     * @param array $data
     *
     * @return Notification
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
