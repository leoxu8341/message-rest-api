<?php

namespace AppBundle\Controller;

use AppBundle\Service\LoginService;
use AppBundle\Traits\CustomViewsTrait;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends Controller
{
    use CustomViewsTrait;

    /**
     * @var LoginService
     */
    private $loginService;

    /**
     * LoginController constructor.
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @return View
     */
    public function loginAction(Request $request)
    {
        list($user, $token) = $this->loginService->login(json_decode($request->getContent(), true));

        return $this->getView(['user' => $user, 'token' => $token]);
    }
}
