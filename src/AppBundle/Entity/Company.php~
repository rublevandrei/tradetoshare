<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyRepository")
 */
class Company
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
     * @Assert\Url()
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location2;
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location3;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location4;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location5;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @ORM\Column(name="yearFounded", type="integer")
     */
    private $yearFounded;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice(
     * choices = { "1","10", "50", "200", "500","1000", "5000", "10000" },
     * message = "Choose a valid size."
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $size;

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
    private $industry;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=2000)
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $blocked = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="companies")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vote", mappedBy="company", cascade={"persist"})
     */
    private $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
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
     * @return Company
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
     * Set link
     *
     * @param string $link
     *
     * @return Company
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Company
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set location2
     *
     * @param string $location2
     *
     * @return Company
     */
    public function setLocation2($location2)
    {
        $this->location2 = $location2;

        return $this;
    }

    /**
     * Get location2
     *
     * @return string
     */
    public function getLocation2()
    {
        return $this->location2;
    }

    /**
     * Set location3
     *
     * @param string $location3
     *
     * @return Company
     */
    public function setLocation3($location3)
    {
        $this->location3 = $location3;

        return $this;
    }

    /**
     * Get location3
     *
     * @return string
     */
    public function getLocation3()
    {
        return $this->location3;
    }

    /**
     * Set location4
     *
     * @param string $location4
     *
     * @return Company
     */
    public function setLocation4($location4)
    {
        $this->location4 = $location4;

        return $this;
    }

    /**
     * Get location4
     *
     * @return string
     */
    public function getLocation4()
    {
        return $this->location4;
    }

    /**
     * Set location5
     *
     * @param string $location5
     *
     * @return Company
     */
    public function setLocation5($location5)
    {
        $this->location5 = $location5;

        return $this;
    }

    /**
     * Get location5
     *
     * @return string
     */
    public function getLocation5()
    {
        return $this->location5;
    }

    /**
     * Set yearFounded
     *
     * @param integer $yearFounded
     *
     * @return Company
     */
    public function setYearFounded($yearFounded)
    {
        $this->yearFounded = $yearFounded;

        return $this;
    }

    /**
     * Get yearFounded
     *
     * @return integer
     */
    public function getYearFounded()
    {
        return $this->yearFounded;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return Company
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Company
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
     * Set industry
     *
     * @param string $industry
     *
     * @return Company
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * Get industry
     *
     * @return string
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Company
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
     * Set logo
     *
     * @param string $logo
     *
     * @return Company
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set blocked
     *
     * @param boolean $blocked
     *
     * @return Company
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Company
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
     * Add vote
     *
     * @param \AppBundle\Entity\Vote $vote
     *
     * @return Company
     */
    public function addVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \AppBundle\Entity\Vote $vote
     */
    public function removeVote(\AppBundle\Entity\Vote $vote)
    {
        $this->votes->removeElement($vote);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
