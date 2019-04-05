<?php

namespace AppBundle\Repository;

use DateTime;
use \Doctrine\DBAL\Types\Type;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByUserAndConnections($ids, $date = null)
    {
        $dates = [
            'year' => '1 year',
            '6-month' => '6 month',
            '3-month' => '3 month',
            'month' => '1 month',
            'week' => 'week',
            'yesterday' => '1 day',
            'today' => '0 day'
        ];

        $qb = $this->createQueryBuilder('p')
            ->where('p.user in (:id)')
            ->andWhere('p.tradeland is NULL')
            ->andWhere('p.createdAt <= :dateTime');

        if (!is_null($date) and array_key_exists($date, $dates)) {
            $fromDate = new DateTime('- ' . $dates[$date]);
            $qb->andWhere('p.createdAt >= :date')
                ->setParameter('date', new DateTime($fromDate->format('Y-m-d')), Type::DATETIME);
        }

        $qb->setParameter('id', $ids)
            ->setParameter('dateTime', new DateTime(), Type::DATETIME)
            ->addSelect('c')
            ->leftJoin('p.comments', 'c')
            ->addSelect('u')
            ->leftJoin('c.user', 'u')
            ->setMaxResults(10)
            ->orderBy('p.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
