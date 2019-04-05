<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Common\Collections\ArrayCollection;
/**
 * Network
 *
 * @ORM\Table(name="network")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NetworkRepository")
 */
class Network
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="connections")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="fromConnections")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $fromUser;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $status;


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
     * Set status
     *
     * @param string $status
     *
     * @return Network
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Network
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
     * Set fromUser
     *
     * @param \AppBundle\Entity\User $fromUser
     *
     * @return Network
     */
    public function setFromUser(\AppBundle\Entity\User $fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

}
