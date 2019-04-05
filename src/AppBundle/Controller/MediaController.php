<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Media controller.
 *
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * @Route("/upload", name="upload_media")
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadAction(Request $request)
    {
        $session = new Session();
        $directory = $this->container->getParameter('post_directory');
        $images = $session->get('media') ?: [];
        foreach ($request->files as $uploadedFile) {

            $images[] = $name = $uploadedFile->getClientOriginalName();

            $uploadedFile->move($directory, $name);
        }

        $session->set('media', $images);

        return new JsonResponse($images);

        $session = new Session();

        $media = $session->get('media') ?: [];
        $test = [];
        foreach ($request->files as $uploadedFile) {
            $ext = $uploadedFile->getClientOriginalExtension();
            $name = $uploadedFile->getClientOriginalName();
            if (in_array($ext, [''])) {
                array_push($media['video'], $name);
                $directory = $this->container->getParameter('post_video_directory');
            } else {
                array_push($media['image'], $name);
                $directory = $this->container->getParameter('post_directory');
            }
            $test[] =  $request->files;

            $uploadedFile->move($directory, $name);
        }
        $session->set('test', $test);
        $session->set('media', $media);


        return new JsonResponse($media);
    }
}
