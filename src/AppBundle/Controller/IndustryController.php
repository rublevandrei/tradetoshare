<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Industry controller.
 *
 * @Route("/industry")
 */
class IndustryController extends Controller
{
    protected $industries = [];

    /**
     * Lists all Industry entities.
     *
     * @Route("/",
     *     options = { "expose" = true },
     *     name="industry_index",
     * )
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function industryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $industries = $em->getRepository('AppBundle:Industry')->findByIndustry($request->request->get('industry'));

        foreach ($industries as $industry) {
            $this->industries[] = $industry->getName();
        }

        return new JsonResponse(['success' => $this->industries]);
    }
}
