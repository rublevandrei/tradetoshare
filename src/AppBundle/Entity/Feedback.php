<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Feedback
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeedbackRepository")
 */
class Feedback
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
     * @Assert\Length(min=3)
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=1)
     * @ORM\Column(type="text")
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
     * Set name
     *
     * @param string $name
     *
     * @return Feedback
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
     * Set email
     *
     * @param string $email
     *
     * @return Feedback
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Feedback
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Feedback
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
}
