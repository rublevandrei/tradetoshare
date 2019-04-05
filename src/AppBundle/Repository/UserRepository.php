<?php

namespace AppBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
	 public function findAllWithoutUser($userId)
    {
    	$qb = $this->createQueryBuilder('u')
            ->where('u.id not in (:id)')
       //     ->andWhere('u.enabled = :enabled')
    //        ->andWhere('u.locked = :locked')
            ->setParameters(['id' => $userId/*, 'enabled' => true, 'locked' => false*/]);

        return $qb->getQuery()->getResult();
    }
}
