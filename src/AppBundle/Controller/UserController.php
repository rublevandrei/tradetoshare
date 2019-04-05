<?php

namespace AppBundle\Controller;

//use AppBundle\Form\PasswordType;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\User;
use AppBundle\Entity\Provider;
use AppBundle\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * Show User dashboard.
     *
     * @Route("/{user}", name="user_show", requirements={ * "user": "\d+" * })
     * @Method({"GET", "POST"})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(User $user)
    {
//        dump($user->isEnabled()); die();
        $em = $this->getDoctrine()->getManager();
//
        $connections = $em->getRepository('AppBundle:Network')->findAcceptedUserConnections($user->getId());
        $count = count($connections);
//        $ids = [$user];
//        foreach ($connections as $connection) {
//            if (!in_array($connection->getUser(), $ids)) {
//                $ids[] = $connection->getUser();
//            }
//
//            if (in_array($connection->getFromUser(), $ids)) {
//                continue;
//            }
//            $ids[] = $connection->getFromUser();
//        }


        return $this->render('AppBundle:User:index.html.twig', [
            'user' => $user,
            'count' => $count
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/edit", name="user_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $avatar = $user->getAvatar();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {

            $file = $user->getAvatar();
            if (!empty($file)) {
                $fileName = $this->get('app.image_uploader')->upload($file, $this->container->getParameter('avatar_directory'));
                $user->setAvatar($fileName);
            } else {
                $user->setAvatar($avatar);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }


        return $this->render('AppBundle:User:edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/password", name="password_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function passwordAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('password_edit'))
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])->getForm();

        /*   $form = $this->createForm(PasswordType::class, $user,[
                   'action' => $this->generateUrl('password_edit', ['id' => $user->getId()])
               ]
           );   */
        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
            $password = $form["password"]->getData();
            $passwordEncoder = $this->container->get('security.password_encoder');
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', ['user' => $user->getId()]);
        }

        return $this->render('AppBundle:User:_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * change  User status.
     *
     * @Route("/status/{status}", name="user_status")
     * @Method({"POST"})
     * @param $status
     * @return JsonResponse
     */
    public function StatusAction($status)
    {
//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
//        }

        $user = $this->getUser();
        $user->setStatus($status);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['status' => $status]);
    }

    /**
     * change  User status.
     *
     * @Route("/connect", name="user_connect")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function connectAction()
    {
        return $this->render('AppBundle:User:connect.html.twig');
    }

    /**
     * Instagram question.
     *
     * @Route("/instagram-question", name="user_instagram_question")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function instagramQuestionAction()
    {
    	$userInformation = $this->get('session')->get('instagram-profile-data');
        return $this->render('AppBundle:User:instagram-question.html.twig', [
            'userInformation' => $userInformation,
        ]);
    }

    /**
     * Instagram question.
     *
     * @Route("/instagram-connect", name="user_instagram_connect")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function instagramConnectAction()
    {
    	$providerInformation = $this->get('session')->get('instagram-provider-data');
    	$provider_id = $providerInformation['provider_id'];
    	$accessToken = $providerInformation['token'];
    	$user = $this->get('security.token_storage')->getToken()->getUser();  
    	
    	$em = $this->getDoctrine()->getManager();	               	

        if (null !== $provider = $em->getRepository('AppBundle:Provider')->findOneBy(['user' => $user->getId(), 'name' => 'instagram', 'provider_id' => $provider_id])) {
            $provider->setAccessToken($accessToken);
        } else {
            $provider = new Provider();
            $provider->setUser($user);
            $provider->setName('instagram');
            $provider->setProviderId($provider_id);
            $provider->setAccessToken($accessToken);
        }

        $em->persist($provider);
        $em->flush();
    	
    	header('Location: /user/connect'); die();
    }
    
    /**
     * Delete User social provider.
     *
     * @Route("/provider/{provider}", name="user_delete_provider", requirements={
     *     "provider": "google|twitter|facebook|instagram|vkontakte|foursquare|pinterest|flickr|imgur|odnoklassniki|tumblr"
     * })
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProviderAction($provider)
    {
        $item = $this->getUser()->getProviders()->matching(Criteria::create()->where(Criteria::expr()->eq("name", $provider)));
        if (isset($item[0])) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($item[0]);
            $em->flush();
        }

        return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
    }
}
