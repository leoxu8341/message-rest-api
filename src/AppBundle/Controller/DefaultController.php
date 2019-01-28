<?php

namespace AppBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/test", name="homepage", methods={"GET"})
     * @return View
     */
    public function getUserAction(Request $request)
    {
        return View::create(['success' => true, 'message' => 'System Available'], Response::HTTP_OK );
    }
}
