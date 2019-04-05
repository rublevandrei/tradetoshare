<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Industry
 *
 * @ORM\Table(name="industry")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndustryRepository")
 */
class Industry
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

     public function jsonSerialize()
    {
        return [
            'name' => $this->name,
        ];
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Industry
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
}
