<?php

namespace AppBundle\Entity;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * FOSGroup
 *
 * @ORM\Table(name="fos_group")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FOSGroupRepository")
 */
class FOSGroup extends BaseGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
