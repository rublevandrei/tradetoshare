<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="messages")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="messagesFrom")
     * @ORM\JoinColumn(name="from_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $from;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="text", length=255)
     */
    private $message;
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
     * Set message
     *
     * @param string $message
     *
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Message
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
     * Set from
     *
     * @param \AppBundle\Entity\User $from
     *
     * @return Message
     */
    public function setFrom(\AppBundle\Entity\User $from = null)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return \AppBundle\Entity\User
     */
    public function getFrom()
    {
        return $this->from;
    }
}
