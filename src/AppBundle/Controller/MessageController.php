<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * Company controller.
 *
 * @Route("/message")
 */
class MessageController extends Controller
{
    /**
     * Lists all Message entities.
     *
     * @Route("/", name="message_index")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('AppBundle:Message')->findBy(['user'=>$this->getUser()]);

        return $this->render('AppBundle:Message:index.html.twig', ['messages' => $messages]);
    }
}
