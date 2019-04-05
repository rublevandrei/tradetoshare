<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Photo;
use AppBundle\Form\PhotoType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * Photo controller.
 *
 * @Route("/photos")
 */
class PhotoController extends Controller
{
     /**
     * Lists all Photo entities.
     *
     * @Route("/", name="photo_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $photos = $em->getRepository('AppBundle:Photo')->findPhotosByUser($this->getUser()->getId());

        return $this->render('AppBundle:Photo:index.html.twig', [
            'photos' => $photos
        ]);
    }

    /**
     * Creates a new Photo entity.
     *
     * @Route("/new", name="photo_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $photo->setUser($this->getUser());
            $file = $photo->getImage();
            if ($file) {
                $fileName = $this->get('app.image_uploader')->upload($file, $this->container->getParameter('photo_directory'));
                $photo->setImage($fileName);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($photo);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'photo  successfully created!');
            return $this->redirectToRoute('photo_index');
        }

        return $this->render('AppBundle:Photo:new.html.twig', [
            'photo' => $photo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Photo entity.
     *
     * @Route("/{id}", name="photo_show")
     * @Method("GET")
     * @param Photo $photo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Photo $photo)
    {

        $em = $this->getDoctrine()->getManager();
        $photos = $em->getRepository('AppBundle:Photo')->findPhotosByUser($this->getUser()->getId());

        return $this->render('AppBundle:Photo:show.html.twig', [
            'photo' => $photo,
            'photos' => $photos
        ]);
    }

    /**
     * Displays a form to edit an existing Photo entity.
     *
     * @Route("/{id}/edit", name="photo_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Photo $photo
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Photo $photo)
    {
        $editForm = $this->createForm('AppBundle\Form\PhotoUpdateType', $photo);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($photo);
            $em->flush();

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('AppBundle:Photo:edit.html.twig', [
            'photo' => $photo,
            'form' => $editForm->createView()
        ]);
    }

    /**
    * @param Request  $request
    * @param BlogPost $photo
    * @return \Symfony\Component\HttpFoundation\RedirectResponse
    *
    * @Route("/{id}/delete", name="photo_delete")
    **/
    public function deleteAction(Request $request, Photo $photo)
    {
        if ($photo === null) {
            return $this->redirectToRoute('photo_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($photo);
        $em->flush();

        return $this->redirectToRoute('photo_index');
    }

}
