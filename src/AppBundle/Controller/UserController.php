<?php

namespace AppBundle\Controller;

use AppBundle\Service\UserService;
use AppBundle\Traits\CustomViewsTrait;
use Doctrine\DBAL\Connection;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    use CustomViewsTrait;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @param Connection $connection
     */
    public function __construct(
        UserService $userService,
        Connection $connection
    ) {
        $this->userService = $userService;
        $this->connection = $connection;
    }

    /**
     * @Route("/users", name="create_new_user", methods={"POST"})
     *
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function createUserAction(Request $request)
    {
        $this->connection->beginTransaction();

        try {
            $user = $this->userService->createNewUser($request);
            $this->userService->storeUser($user);

            $this->connection->commit();

            return $this->getCreatedView($user);
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}