<?php

namespace AppBundle\Repository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VideoRepository extends \Doctrine\ORM\EntityRepository
{

    public function findVideosByUser($user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameters(['user' => $user])
            ->getQuery()
            ->getResult();
    }
}
