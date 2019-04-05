<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Post;
use AppBundle\Entity\Review;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Creates a new Comment entity.
     *
     * @Route("/new/{post}", name="comment_new")
     * @Method({"GET", "POST"})
     * @param Post $post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Post $post, Request $request)
    {
        $comment = new Comment();

        $comment->setPost($post);
        $comment->setText($request->query->get('text'));
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_new', ['post' => $post->getId()])
        ]);

        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setPost($post);

            $em = $this->getDoctrine()->getManager();

            if($post->getUser() != $this->getUser()){
                $notify = new Notification();
                $notify->setUser($post->getUser());
                $notify->setType('comment');
                $notify->setData([
                    'post_id' => $post->getId(),
                    'user_id' => $this->getUser()->getId(),
                    'user_name' => $this->getUser()->getName(),
                ]);
                $em->persist($notify);
            }


            $em->persist($comment);
            $em->flush();
            if (is_null($post->getTradeland())) {
                return $this->redirectToRoute('post_index', ['user' => $post->getUser()->getId()]);
            } else {
                return $this->redirectToRoute('tradeland_show', ['tradeland' => $post->getTradeland()->getId()]);
            }
        }

        return $this->render('AppBundle:Comment:new.html.twig', [
            'comment' => $comment,
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new ArticleComment entity.
     *
     * @Route("/article_comment_new/{post}", name="article_comment_new")
     * @Method({"GET", "POST"})
     * @param Article $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newArticleCommentAction(Article $article, Request $request)
    {
        $comment = new Review();

        $comment->setArticle($article);
        $comment->setText($request->query->get('text'));
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('article_show', ['article' => $article->getId()]);
        }

        return $this->render('AppBundle:Comment:new.html.twig', [
            'comment' => $comment,
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }
}
