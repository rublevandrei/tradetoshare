<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * Article controller.
 *
 * @Route("/article")
 */
class ArticleController extends Controller
{
	 /**
     * Lists all Article entities.
     *
     * @Route("/", name="article_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $letter = $request->get('letter');
        $letter = empty($letter) ? 'a' : $letter;

        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findArticlesByLetter($letter);

        return $this->render('AppBundle:Article:index.html.twig', [
            'articles' => $articles,
            'chosenLetter' => $letter
        ]);
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/new", name="article_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $article->setUser($this->getUser());
            $file = $article->getImage();
            if ($file) {
                $fileName = $this->get('app.image_uploader')->upload($file, $this->container->getParameter('article_directory'));
                $article->setImage($fileName);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Article  successfully created!');
            return $this->redirectToRoute('article_index');
        }

        return $this->render('AppBundle:Article:new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Article entity.
     *
     * @Route("/{id}", name="article_show")
     * @Method("GET")
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Article $article)
    {
        if($article->getApproved() !== true){
            throw $this->createNotFoundException('The article does not exist');
        }

        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findSameArticles($article, $this->getUser());

        $deleteForm = $this->createDeleteForm($article);

        return $this->render('AppBundle:Article:show.html.twig', [
            'article' => $article,
            'delete_form' => $deleteForm->createView(),
            'articles' => $articles
        ]);
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{id}/edit", name="article_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Article $article)
    {
        $deleteForm = $this->createDeleteForm($article);
        $editForm = $this->createForm('AppBundle\Form\ArticleType', $article);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('AppBundle:Article:new.html.twig', [
            'article' => $article,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/{id}", name="article_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Article $article)
    {
        $form = $this->createDeleteForm($article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('article_index');
    }

    /**
     * Creates a form to delete a Article entity.
     *
     * @param Article $article The Article entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('article_delete', ['id' => $article->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
