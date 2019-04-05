<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /*$em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByEmail('pdandreyv@gmail.com');
        
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
        /*
        $password = 'Troica123';
            $passwordEncoder = $this->container->get('security.password_encoder');
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
            $em->persist($user);
            $em->flush();
        
        var_dump($user->getId()); exit;*/
        if (!is_null($this->getUser())) {
            return $this->redirectToRoute('post_index', ['user' => $this->getUser()->getId()]);
        }

        return $this->render('index.html.twig');
    }

    /**
     * @Route("/payment", name="payment_index")
     */
    public function paymentAction()
    {
        return $this->render('AppBundle:Payment:index.html.twig');
    }

    /**
     * @Route("/test", name="test_index")
     */
    public function testAction()
    {
        return $this->render('AppBundle:Company:_form.html.twig');
    }

    /**
     * upload Image to the company.
     *
     * @Route("/company_image", name="company_image")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {

//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
//        }

        $form = $this->createFormBuilder()
            ->add('logo', FileType::class, [
                'constraints' => new Image(['mimeTypes' => ["image/jpg", "image/jpeg", "image/gif", "image/png"]])
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $name = $this->get('app.image_uploader')->upload($form['logo']->getData(), $this->container->getParameter('company_directory'));
            return new JsonResponse(['success' => $name]);
        }
        return $this->render('AppBundle:Company:image.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * upload Image to the company.
     *
     * @Route("/video", name="video")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadVideo(Request $request)
    {
//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
//        }
        //   return new JsonResponse(['success' => $_FILES]);
        $videoForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('post_new'))
            ->add('video', FileType::class)->getForm();

        $videoForm->handleRequest($request);
        if (!$request->isXmlHttpRequest() or !$request->isMethod($request::METHOD_POST)) {
            return new JsonResponse(['message' => 'You can access this only using Ajax!'], 400);
        }

        $name = basename($_FILES['video']['name']);
        $uploadfile = $this->container->getParameter('post_video_directory') . '/' . basename($_FILES['video']['name']);

        if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadfile)) {
            return new JsonResponse(['success' => $name]);
        } else {
            return new JsonResponse(['error' => 'Возможная атака с помощью файловой загрузки!']);
        }
    }

}
