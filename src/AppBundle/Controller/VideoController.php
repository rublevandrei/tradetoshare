<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Video;
use AppBundle\Form\VideoType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * Video controller.
 *
 * @Route("/videos")
 */
class VideoController extends Controller
{
     /**
     * Lists all Video entities.
     *
     * @Route("/", name="video_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('AppBundle:Video')->findVideosByUser($this->getUser()->getId());

        return $this->render('AppBundle:Video:index.html.twig', [
            'videos' => $videos
        ]);
    }

    /**
     * Creates a new Video entity.
     *
     * @Route("/new", name="video_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $video->setUser($this->getUser());
            $video->setYid();
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'video  successfully created!');
            return $this->redirectToRoute('video_index');
        }

        return $this->render('AppBundle:Video:new.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Video entity.
     *
     * @Route("/{id}", name="video_show")
     * @Method("GET")
     * @param Video $video
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Video $video)
    {

        $em = $this->getDoctrine()->getManager();
        $videos = $em->getRepository('AppBundle:Video')->findVideosByUser($this->getUser()->getId());

        return $this->render('AppBundle:Video:show.html.twig', [
            'video' => $video,
            'videos' => $videos
        ]);
    }

    /**
     * Displays a form to edit an existing Video entity.
     *
     * @Route("/{id}/edit", name="video_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Video $video
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Video $video)
    {
        $editForm = $this->createForm('AppBundle\Form\VideoUpdateType', $video);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('video_index');
        }

        return $this->render('AppBundle:Video:edit.html.twig', [
            'video' => $video,
            'form' => $editForm->createView()
        ]);
    }

    /**
    * @param Request  $request
    * @param Video $video
    * @return \Symfony\Component\HttpFoundation\RedirectResponse
    *
    * @Route("/{id}/delete", name="video_delete")
    **/
    public function deleteAction(Request $request, Video $video)
    {
        if ($video === null) {
            return $this->redirectToRoute('video_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();

        return $this->redirectToRoute('video_index');
    }

}
