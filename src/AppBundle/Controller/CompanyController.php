<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Company;
use AppBundle\Form\CompanyType;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Company controller.
 *
 * @Route("/company")
 */
class CompanyController extends Controller
{
    /**
     * Lists all Company entities.
     *
     * @Route("/", name="company_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $letter = $request->get('letter');
        $letter = empty($letter) ? 'a' : $letter;

        $em = $this->getDoctrine()->getManager();

        $companies = $em->getRepository('AppBundle:Company')->companyCount($letter);

        return $this->render('AppBundle:Company:index.html.twig', [
            'companies' => $companies,
            'chosenLetter' => $letter
        ]);
    }

    /**
     * Creates a new Company entity.
     *
     * @Route("/new", name="company_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request); // заполняем форму данными

        if ($form->isValid()) {
//            $file = $company->getLogo();
//            if ($file) {
//                $fileName = $this->get('app.image_uploader')->upload($file, $this->container->getParameter('company_directory'));
//                $company->setLogo($fileName);
//            }

            $company->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Company  successfully created!');
            return $this->redirectToRoute('company_index');
            //return $this->redirectToRoute('company_show', ['id' => $company->getId()]);
        }

        return $this->render('AppBundle:Company:new.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Lists of user Company entities.
     *
     * @Route("/user", name="user_companies")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userAction()
    {
        $em = $this->getDoctrine()->getManager();
        $companies = $em->getRepository('AppBundle:Company')->userCompanies($this->getUser()->getId());

        return $this->render('AppBundle:Company:user.html.twig', [
            'companies' => $companies
        ]);
    }

    /**
     * Add a Post entity to auth user timeline.
     *
     * @Route("/vote/{company}", name="company_vote")
     * @Method("POST")
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voteAction(Company $company)
    {
        $em = $this->getDoctrine()->getManager();

        $vote = $em->getRepository('AppBundle:Vote')->findOneBy(['user'=>$this->getUser(), 'company'=>$company]);
        if(is_null($vote)){

            $vote = new \AppBundle\Entity\Vote();
            $vote->setCompany($company);
            $vote->setUser($this->getUser());

            $notify = new Notification();
            $notify->setUser($company->getUser());
            $notify->setType('vote');
            $notify->setData([
                'user_id' => $this->getUser()->getId(),
                'user_name' => $this->getUser()->getName(),
            ]);

            $em->persist($vote);
            $em->persist($notify);
            $response = 'created';
        }else{
            $em->remove($vote);
            $response = 'removed';
        }
        $em->flush();

        return new JsonResponse(['message' => $response]);
    }

    /**
     * Finds and displays a Company entity.
     *
     * @Route("/{id}", name="company_show")
     * @Method("GET")
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Company $company)
    {
        if($company->getBlocked() === true){
            throw $this->createNotFoundException('The company has been blocked');
        }
        $deleteForm = $this->createDeleteForm($company);

        return $this->render('AppBundle:Company:show.html.twig', [
            'company' => $company,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Company entity.
     *
     * @Route("/{id}/edit", name="company_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Company $company)
    {
        $deleteForm = $this->createDeleteForm($company);
     //   $logo = $company->getLogo();
        $editForm = $this->createForm('AppBundle\Form\CompanyType', $company);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_index');
        }

        return $this->render('AppBundle:Company:new.html.twig', [
            'company' => $company,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }


    /**
     * Deletes a Company entity.
     *
     * @Route("/{id}", name="company_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Company $company
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Company $company)
    {
        $form = $this->createDeleteForm($company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($company);
            $em->flush();
        }

        return $this->redirectToRoute('company_index');
    }

    /**
     * Creates a form to delete a Company entity.
     *
     * @param Company $company The Company entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Company $company)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('company_delete', ['id' => $company->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}