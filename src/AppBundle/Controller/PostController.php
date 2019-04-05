<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Entity\Tradeland;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Post controller.
 *
 * @Route("/post")
 */
class PostController extends Controller
{

    /**
     * Show User dashboard.
     *
     * @Route("/timeline/{user}", name="post_index", requirements={ * "user": "\d+" * })
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function timelineAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();
        //$images = $session->all();dump($images); die();
        $session->remove('media');

        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($user->getId());

        $ids = [$user];
        foreach ($connections as $connection) {
            if (!in_array($connection->getUser(), $ids)) {
                $ids[] = $connection->getUser();
            }

            if (in_array($connection->getFromUser(), $ids)) {
                continue;
            }
            $ids[] = $connection->getFromUser();
        }

        $posts = $em->getRepository('AppBundle:Post')->findByUserAndConnections($ids, $request->get('date'));
        $images = $em->getRepository('AppBundle:Media')->findAll();

        return $this->render('AppBundle:Post:index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'images' => $images
        ]);
    }


    /**
     * Creates a new Post entity.
     *
     * @Route("/new/{tradeland}", defaults={"tradeland" = null}, name="post_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Tradeland $tradeland
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Tradeland $tradeland = null)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post, [
            'action' => $this->generateUrl('post_new',
                ['tradeland' => is_null($tradeland) ? null : $tradeland->getId()])
        ]);

        $form->handleRequest($request);

        if ($request->isMethod($request::METHOD_POST)) {

            if ($form->isSubmitted() && $form->isValid()) {

                if (!is_null($tradeland) and ($tradeland->getOwner() == $this->getUser()
                        or !empty($tradeland->getUsers()->filter(
                            function ($entry) {
                                return $entry == $this->getUser();
                            }
                        )->getValues()))
                ) {
                    $post->setTradeland($tradeland);
                    $location = $this->redirectToRoute('tradeland_show', ['tradeland' => $tradeland->getId()]);
                } else {
                    $location = $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
                }
                $post->setCreatedAt(new \DateTime($form->get('createdAt')->getData()));
                $post->setUser($this->getUser());

                $em = $this->getDoctrine()->getManager();

                $em->persist($post);

                $em->flush();
                $session = new Session();

                $images = $session->get('media') ?: [];

                foreach ($images as $image) {
                    $media = new Media();
                    $media->setName($image);
                    $media->setTypeId(1);
                    $media->setPost($post);
                    $em->persist($media);
                }
                $em->flush();
            }

            if (in_array($_POST['appbundle_post']['post_to'], ['facebook', 'twitter', 'tumblr', 'imgur'])) {
                $location = $this->redirectToRoute('post_feed', ['provider' => $_POST['appbundle_post']['post_to'], 'post' => $post->getId()]);
            }

            return $location;
        }

        return $this->render('AppBundle:Post:new.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }


    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
        }

        return $this->render('AppBundle:Post:edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/{id}", name="post_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
    }

    /**
     * Creates a form to delete a Post entity.
     *
     * @param Post $post The Post entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Add a Post entity to auth user timeline.
     *
     * @Route("/share/{post}", name="post_share")
     * @Method("GET")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function shareAction(Post $post)
    {
        if ($this->getUser() !== $post->getUser()) {
            $new_post = new Post();
            $new_post->setText($post->getText());
            $new_post->setOriginUser($post->getUser());
//            $new_post->setImage($post->getImage());
//            $new_post->setVideo($post->getVideo());
            $new_post->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($new_post);
            $em->flush();
        }

        return $this->redirectToRoute('user_show', ['user' => $post->getUser()->getId()]);
    }

    /**
     * Add a Post entity to auth user timeline.
     *
     * @Route("/feed/{provider}/{post}", name="post_feed", defaults={"post" = null}, requirements={
     *     "provider": "google|twitter|facebook|instagram|vkontakte|foursquare|pinterest|flickr|imgur|odnoklassniki|wordpress|tumblr"
     * })
     * @Method("GET")
     * @param $provider
     * @param Post $post
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function feedAction($provider, Post $post = null, Request $request)
    {
        $redirect_url = $this->generateUrl('post_feed', ['provider' => $provider, 'post' => is_null($post) ? null : $post->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $folder = in_array($provider, ['twitter', 'flickr', 'tumblr']) ? 'One' : 'Two';
        $class_name = 'AppBundle\Provider\Network\\' . $folder . '\\' . ucfirst($provider) . 'Provider';
        $provider_class = new $class_name([
            'client_id' => $this->container->getParameter('social_media.resource_owners.' . $provider . '.client_id'),
            'client_secret' => $this->container->getParameter('social_media.resource_owners.' . $provider . '.client_secret'),
            'redirect_url' => $redirect_url,
        ]);

        if (isset($_REQUEST['code']) or !empty($_GET['oauth_token'])) {
            if (!is_null($post)) {
                try {
                    $provider_class->addPublication([
                        'text' => $post->getText(),
                        'image' => 'http://sport.ua/images/news/0/8/70/orig_334001.jpg'
                    ]);
                } catch (\Exception $e) {
                }
            } else {
                try {
                    $posts = $provider_class->userPosts();
                    $em = $this->getDoctrine()->getManager();
                    foreach ($posts as $post) {
                        $item = $em->getRepository('AppBundle:Post')->findOneBy(['user' => $this->getUser(), 'social_provider' => $provider, 'social_id' => $post['id']]);
                        if (is_null($item)) {
                            $new_post = new Post();
                            $new_post->setUser($this->getUser());

                            $new_post->setText($post['description'] . (isset($post['image']) ? ' <img src="' . $post['image'] . '" />' : ''));
                            $new_post->setSocialProvider($provider);
                            $new_post->setSocialId($post['id']);
                            $new_post->setCreatedAt(new \DateTime("now"));
                            $em->persist($new_post);
                        }
                    }
                    $em->flush();
                } catch (\Exception $e) {
                    dump($e);
                    die();
                    return $this->redirectToRoute('post_feed', ['provider' => $provider]);
                }
            }
        } else {
            return $provider_class->redirect();
        }

        return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
    }
}