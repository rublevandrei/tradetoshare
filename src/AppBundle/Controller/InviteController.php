<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Provider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\Entity\User;

/**
 * Invite controller.
 *
 * @Route("/invite")
 */
class InviteController extends Controller
{
    protected $contacts = [];
    protected $batchSize = 20;

    /**
     * Lists all Industry entities.
     *
     * @Route("/", name="invite_index")
     */
    public function industryAction()
    {
        return $this->render('AppBundle:Invite:index.html.twig');
    }

    public $httpClient;

    /**
     * Show contacts by provider.
     *
     * @Route("/{provider}", name="contacts_show", requirements={
     *     "provider": "google|twitter|facebook|instagram|vkontakte|foursquare|yahoo|pinterest|flickr|imgur|odnoklassniki|wordpress|tumblr|hotmail"
     * })
     * @Method({"GET", "POST"})
     * @param  $provider
     * @param  Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction($provider, Request $request)
    {
        $redirect_url = $this->generateUrl('contacts_show', ['provider' => $provider], UrlGeneratorInterface::ABSOLUTE_URL);
        $folder = in_array($provider, ['twitter', 'flickr', 'tumblr']) ? 'One' : 'Two';
        $class_name = 'AppBundle\Provider\Network\\' . $folder . '\\' . ucfirst($provider) . 'Provider';
        $provider_class = new $class_name([
            'client_id' => $this->container->getParameter('social_media.resource_owners.' . $provider . '.client_id'),
            'client_secret' => $this->container->getParameter('social_media.resource_owners.' . $provider . '.client_secret'),
            'redirect_url' => $redirect_url,
        ]);

        if ($request->isMethod($request::METHOD_POST)) {
            $response = $provider_class->sendInvitation($request->request->get('contacts'));
            if ($response === false) {
                $this->get('session')->getFlashBag()->add('error', "We can't send invitations from this service");
            } else {
                $this->get('session')->getFlashBag()->add('success', 'Invitations have been sent');
            }

            return $this->redirectToRoute('tradeland_invite');
        } elseif (isset($_REQUEST['code']) or !empty($_GET['oauth_token'])) {

            try {
                $this->contacts = $provider_class->userContacts();
            } catch (\Exception $e) {
                return $this->redirectToRoute('contacts_show', ['provider' => $provider]);
            }
            try {
                if (!empty($this->contacts) and in_array($provider, ['twitter', 'yahoo', 'google', 'hotmail'])) {
                    $count = count($this->contacts);

                    $em = $this->getDoctrine()->getManager();
                    for ($i = 0; $i < $count; ++$i) {
                        $username = $this->contacts[$i]['id'];
                        $email = is_null($this->contacts[$i]['email']) ? $username . '@' . $provider . '.com' : $this->contacts[$i]['email'];
                        $user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $username]);
                        if (null === $user)                        {
                            $user = new User;

                            $user->setEmail($email);
                            $user->setUsername($username);
                            $user->setPassword($username);
                            $user->setAvatar($this->contacts[$i]['avatar']);
                            $user->setName($this->contacts[$i]['name']);
                            $user->setLocation($this->contacts[$i]['location']);
                         //   $setter_id = 'set' . ucfirst($provider) . 'Id';

                           // $user->$setter_id($username);


                            $em->persist($user);
                            //   if (($i % $this->batchSize) === 0) {
                            $em->flush();
                            //            $em->clear();
                            //      }

                            $provider_obj = new Provider();
                            $provider_obj->setUser($user);
                            $provider_obj->setName($provider_obj);
                            $provider_obj->setProviderId($username);

                            $em->persist($provider_obj);
                            $em->flush();
                        }
                    }
//                    $em->flush();
//                    $em->clear();
                }
            } catch (\Exception $e) {

            }

        } else {
            return $provider_class->redirect();
        }

        return $this->render('AppBundle:Invite:show.html.twig', [
            'provider' => $provider,
            'contacts' => $this->contacts
        ]);
    }

    /**
     * Invite by emails.
     *
     * @Route("/email", name="invite_email")
     *
     * @Method({"POST"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function EmailAction(Request $request)
    {
//        $default = array('message' => 'Default input value');
//        $form = $this->createFormBuilder($default)
//
//            ->add('email', EmailType::class,[
//                'label' => 'Email',
//                'constraints' =>[
//                    new Email([
//                        'message'=>'This is not the corect email format'
//                    ]),
//                    new NotBlank([
//                        'message' => 'This field can not be blank'
//                    ])
//                ],
//            ])
//            ->getForm();
//
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            // data is an array with "name", "email", and "message" keys
//            $data = $form->getData();
//            // send email
//            // redirect to prevent resubmision
//            var_dump($data);
//        }
//
//        return $this->render('AppBundle:Invite:email.html.twig', [
//            'form' => $form->createView()
//        ]);
//
        $emails = $request->request->get('email');
        if (!empty($emails)) {
            $success = '';
            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {


                    $message = \Swift_Message::newInstance()
                        ->setSubject('Invitation')
                        ->setFrom('info@tts.com')
                        ->setTo($email)
                        ->setBody(
                            $this->renderView(
                                'AppBundle:Email:invitation.html.twig'
                            ),
                            'text/html'
                        );

                    $this->get('mailer')->send($message);
                    $success .= $email . ' ';
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'invalid email ' . $email);
                }

            }
            if (!empty($success)) {
                $this->get('session')->getFlashBag()->add('success', 'Invitation was sending to ' . $success);
            }

        } else {
            $this->get('session')->getFlashBag()->add('error', 'set emails');
        }

        return $this->redirectToRoute('network_index');
    }

    public function validateEmails($emails)
    {

        $errors = array();
        $emails = is_array($emails) ? $emails : array($emails);

        $validator = $this->container->get('validator');

        $constraints = array(
            new \Symfony\Component\Validator\Constraints\Email(),
            new \Symfony\Component\Validator\Constraints\NotBlank()
        );

        foreach ($emails as $email) {

            $error = $validator->validateValue($email, $constraints);

            if (count($error) > 0) {
                $errors[] = $error;
            }
        }

        return $errors;
    }
}
