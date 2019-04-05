<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Notification controller.
 *
 * @Route("/notification")
 */
class NotificationController extends Controller
{
    /**
     * @Route("/", name="notification_index")
     */
    public function indexAction()
    {
        $notifications = $this->getUser()->getNotifications();

        $accepted = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "accepted")));
        $confirmed = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "confirmed")));
        $pending = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "pending")));
        $ignored = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "ignored")));
        $votes = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "vote")));
        $comments = $notifications->matching(Criteria::create()->where(Criteria::expr()->eq("type", "comment")));

        $accepted = new ArrayCollection(array_merge($accepted->toArray(), $confirmed->toArray()));

        $notes = new ArrayCollection(
            array_merge($votes->toArray(), $ignored->toArray(), $comments->toArray())
        );

        return $this->render('AppBundle:Notification:index.html.twig', [
            'count' => $notifications->count(),
            'accepted' => $accepted,
            'pending' => $pending,
            'notes' => $notes,
        ]);
    }


}
