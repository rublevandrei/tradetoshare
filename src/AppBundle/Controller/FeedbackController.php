<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 *  Feedback controller.
 *
 * @Route("/feedback")
 */
class FeedbackController extends Controller
{
    /**
     * Creates a new Feedback entity.
     *
     * @Route("/new", name="feedback_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Request has been sent')
                ->setFrom($feedback->getEmail())
                ->setTo('bagalex@inbox.ru')
                ->setBody(
                    $this->renderView(
                        'AppBundle:Email:feedback.html.twig',
                        ['message' => $feedback->getMessage()]
                    ),
                    'text/html'
                );

            $this->get('mailer')->send($message);
            $this->get('session')->getFlashBag()->add('success', 'Request has been successfully sent!');
            return $this->redirectToRoute('feedback_new');
        }

        return $this->render('AppBundle:Feedback:new.html.twig', [
            'feedback' => $feedback,
            'form' => $form->createView(),
        ]);
    }
}
