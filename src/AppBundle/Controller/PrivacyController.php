<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Privacy controller.
 *
 * @Route("/privacy")
 */
class PrivacyController extends Controller
{
    /**
     * Lists all Users with connection status.
     * @Route("/", name="privacy_index")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Privacy:index.html.twig');
    }
}
