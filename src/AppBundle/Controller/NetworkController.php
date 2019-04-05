<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Network;
use AppBundle\Entity\Notification;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

/**
 * Network controller.
 *
 * @Route("/network")
 */
class NetworkController extends Controller
{
    /**
     * Lists all Users with connection status.
     * @Route("/", name="network_index")
     */
    public function networkAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAllWithoutUser($this->getUser()->getId());
        $connections = $em->getRepository('AppBundle:Network')->findAllUserConnections($this->getUser()->getId());

        $ids = $statuses = [];
        foreach ($connections as $connection) {
            if (!in_array($connection->getUser(), $ids) and $connection->getUser() != $this->getUser()) {
                $ids[] = $connection->getUser();
                $statuses[$connection->getStatus()][$connection->getUser()->getId()] = $connection->getId();
            }

            if (in_array($connection->getFromUser(), $ids) or $connection->getFromUser() === $this->getUser()) {
                continue;
            }
            $ids[] = $connection->getFromUser();
            $statuses[$connection->getStatus()][$connection->getFromUser()->getId()] = $connection->getId();
        }

        return $this->render('AppBundle:Network:index.html.twig', ['users' => $users, 'connections' => $ids, 'statuses' => $statuses]);
    }

    /**
     * @Route("/connect/{user}", name="network_connect")
     * @param User $user

     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectAction(User $user)
    {
        if ($user == $this->getUser() or $user->isEnabled() == false) {
            throw $this->createNotFoundException('The page does not exist');
        }

        $em = $this->getDoctrine()->getManager();

        $connect = $em->getRepository('AppBundle:Network')->findConnection($this->getUser()->getId(), $user->getId());

        if (empty($connect)) {
            $connect = new Network();
            $connect->setStatus('pending');
            $connect->setFromUser($this->getUser());
            $connect->setUser($user);

            $em->persist($connect);
            $em->flush();

            $notify = new Notification();
            $notify->setType('pending');
            $notify->setData([
                'connection_id' => $connect->getId(),
                'user_id' => $this->getUser()->getId(),
                'user_name' => $this->getUser()->getName(),
                'user_avatar' => $this->getUser()->getAvatar()
            ]);

           $notify->setUser($user);

        //    $em->persist($connect);
            $em->persist($notify);
            $response = 'Pending';
        } else {
            $em->remove($connect[0]);
            $response = 'Connect';
        }
        $em->flush();

        return new JsonResponse(['message' => $response]);
    }

    /**
     * @Route("/connect/{connection}", name="network_unfriend")
     * @param Network $connection
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unfriendAction(Network $connection)
    {
        if ($connection->getFromUser() !== $this->getUser() and $connection->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('The page does not exist');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($connection);
        $em->flush();

        return $this->redirectToRoute('network_index');
    }

    /**
     * @Route("/status/{connection}/{status}", name="network_status")
     * @param Network $connection
     * @param  string $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function statusAction(Network $connection, $status)
    {
        if ($connection->getStatus() != 'pending' or !in_array($status, ['accepted', 'ignored']) or ($connection->getUser() != $this->getUser() and $connection->getFromUser() != $this->getUser())) {
            throw $this->createNotFoundException('The page does not exist');
        }
        $connection->setStatus($status);
        $em = $this->getDoctrine()->getManager();

     //   $notes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findBy(['connect' => $connection]);
        $notes = $this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findNetworkUserNotifications();

        foreach ($notes as $note) {
            if(isset($note->getData()['connection_id']) and $note->getData()['connection_id'] == $connection->getId()){
                $em->remove($note);
            }
        }

        if ($status == 'accepted') {
            $note = new Notification();
            $note->setUser($connection->getUser());
            $note->setType('accepted');
            $note->setData([
                'connection_id' => $connection->getId(),
                'user_id' => $connection->getFromUser()->getId(),
                'user_name' => $connection->getFromUser()->getName(),
                'user_avatar' => $connection->getFromUser()->getAvatar()
            ]);

            $notify = new Notification();
            $notify->setUser($connection->getFromUser());
            $notify->setType('confirmed');
            $notify->setData([
                'connection_id' => $connection->getId(),
                'user_id' => $this->getUser()->getId(),
                'user_name' => $this->getUser()->getName(),
                'user_avatar' => $this->getUser()->getAvatar()
            ]);

            $em->persist($note);
            $em->persist($notify);

        } elseif ($status == 'ignored') {
            $notify = new Notification();
            $notify->setUser($connection->getFromUser());
            $notify->setType('ignored');
            $notify->setData([]);
            $em->persist($notify);
            $em->remove($connection);

        }

        $em->flush();

        return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
    }

    /**
     * @Route("/connections", name="user_network")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectionsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($this->getUser()->getId());

        return $this->render('AppBundle:Network:connections.html.twig', ['connections' => $connections]);
    }

    /**
     * @Route("/invite", name="network_invite")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction()
    {
        $em = $this->getDoctrine()->getManager();

        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($this->getUser()->getId());

        return $this->render('AppBundle:Network:invite.html.twig', ['connections' => $connections]);
    }
}
