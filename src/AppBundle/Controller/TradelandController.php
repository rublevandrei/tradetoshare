<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tradeland;
use AppBundle\Form\TradelandType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Network controller.
 *
 * @Route("/tradeland")
 */
class TradelandController extends Controller
{
    /**
     * Creates a new Tradeland entity.
     *
     * @Route("/", name="tradeland_index")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tradelands = $em->getRepository('AppBundle:Tradeland')->getByUser($this->getUser()->getId());
        $other_tradelands = $em->getRepository('AppBundle:Tradeland')->findByNot($this->getUser());

        return $this->render('AppBundle:Tradeland:index.html.twig', [
            'tradelands' => $tradelands,
            'other_tradelands' => $other_tradelands
        ]);
    }

    /**
     * Creates a new Tradeland entity.
     *
     * @Route("/new", name="tradeland_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $tradeland = new Tradeland();
        $form = $this->createForm(TradelandType::class, $tradeland, [
                'action' => $this->generateUrl('tradeland_new')
            ]
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $tradeland->setOwner($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($this->getUser()->getId());

            $users = [];
            foreach ($connections as $connection) {
                if (!in_array($connection->getUser(), $users) and $connection->getUser() != $this->getUser()) {
                    $users[] = $connection->getUser();
                }
                if (in_array($connection->getFromUser(), $users) or $connection->getFromUser() == $this->getUser()) {
                    continue;
                }
                $users[] = $connection->getFromUser();

            }

            foreach ($users as $user) {
                $tradeland->addUser($user);
                $user->addTradeland($tradeland);
            }
              $em->persist($tradeland);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'You have successfully create your tradeland!');

            return $this->redirectToRoute('tradeland_index');
        }

        return $this->render('AppBundle:Tradeland:new.html.twig', [
            'tradeland' => $tradeland,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/connect/{tradeland}", name="tradeland_connect")
     * @param Tradeland $tradeland
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connectAction(Tradeland $tradeland)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $connect = $em->getRepository('AppBundle:Tradeland')->getTradelands($user, $tradeland);
        if (empty($connect)) {
            $tradeland->addUser($user);
            $user->addTradeland($tradeland);;
            $response = 'Leave';
        } else {
            $user->removeTradeland($tradeland);
            $tradeland->removeUser($user);
            $em->persist($user);
            $response = 'Join';
        }
        $em->flush();

        return new JsonResponse(['message' => $response]);
    }

    /**
     * @Route("/invite", name="tradeland_invite")
     * @return Route
     */
    public function inviteAction()
    {
        //    $em = $this->getDoctrine()->getManager();

//        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($this->getUser()->getId());

//        $message = \Swift_Message::newInstance()
//            ->setSubject('Invitation')
//            ->setFrom('info@tts.com')
//            ->setTo('bagalex@inbox.ru')
//            ->setBody(
//                $this->renderView(
//                    'AppBundle:Email:invitation.html.twig'
//                ),
//                'text/html'
//            );
//
//        $this->get('mailer')->send($message);
        return $this->render('AppBundle:Tradeland:invite.html.twig');
    }

    /**
     * Finds and displays a Company entity.
     *
     * @Route("/members/{tradeland}", name="tradeland_members")
     * @Method("GET")
     * @param Tradeland $tradeland
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function membersAction(Tradeland $tradeland)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $tradeland->getUsers();
        $connections = $em->getRepository('AppBundle:Network')->findAllUserConnections($this->getUser()->getId());

        $ids = $statuses = [];
        foreach ($connections as $connection) {
            if (!in_array($connection->getUser(), $ids) and $connection->getUser() != $this->getUser()) {
                $ids[] = $connection->getUser();
                // $connection->getUser()->status = 'test';
                $statuses[$connection->getStatus()][$connection->getUser()->getId()] = $connection->getId();
            }

            if (in_array($connection->getFromUser(), $ids) or $connection->getFromUser() === $this->getUser()) {
                continue;
            }
            $ids[] = $connection->getFromUser();
            $statuses[$connection->getStatus()][$connection->getFromUser()->getId()] = $connection->getId();
        }

        return $this->render('AppBundle:Tradeland:members.html.twig', ['tradeland' => $tradeland, 'users' => $users, 'connections' => $ids, 'statuses' => $statuses]);

    }

    /**
     * Finds and displays a Company entity.
     *
     * @Route("/{tradeland}", name="tradeland_show")
     * @Method("GET")
     * @param Tradeland $tradeland
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Tradeland $tradeland)
    {
        if($tradeland->getBlocked() === true){
            throw $this->createNotFoundException('The tradeland has been blocked');
        }

        $em = $this->getDoctrine()->getManager();
        $users = [];//$em->getRepository('AppBundle:Tradeland')->findGroupsByUser($this->getUser()->getId());
        $posts = $em->getRepository('AppBundle:Post')->findBy(['tradeland' => $tradeland]);

        return $this->render('AppBundle:Tradeland:show.html.twig', [
            'tradeland' => $tradeland,
            'users' => $users,
            'posts' => $posts
        ]);
    }


}
