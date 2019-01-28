<?php

namespace AppBundle\Controller;

use AppBundle\Service\MessageService;
use AppBundle\Service\UserService;
use Doctrine\DBAL\Connection;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends Controller
{
    /**
     * @var MessageService
     */
    private $messageService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * MessageController constructor.
     * @param MessageService $messageService
     * @param UserService $userService
     * @param Connection $connection
     */
    public function __construct(
        MessageService $messageService,
        UserService $userService,
        Connection $connection
    ) {
        $this->messageService = $messageService;
        $this->userService = $userService;
        $this->connection = $connection;
    }


    /**
     * @Route("/messages", name="get_all_messages", methods={"GET"})
     *
     * @param Request               $request
     * @param ParamFetcherInterface $paramFetcher
     *
     * @Annotations\QueryParam(
     *     name="sorting",
     *     default="DESC",
     *     requirements="DESC|ASC",
     *     nullable=true,
     *     strict=true,
     *     description="sort direction"
     * )
     *
     * @Annotations\QueryParam(
     *    name="page_limit",
     *    default="10",
     *    nullable=true,
     *    requirements="\d+",
     *    strict=true,
     *    description="How many to return"
     * )
     *
     * @Annotations\QueryParam(
     *    name="page_index",
     *    default="1",
     *    nullable=true,
     *    requirements="\d+",
     *    strict=true,
     *    description="page number"
     * )
     *
     * @return View
     */
    public function getMessagesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $messages = $this->messageService->getMessages($paramFetcher);

        return $this->messageService->getPaginatedView($paramFetcher, $messages);
    }

    /**
     * @Route("/messages", name="create_new_messages", methods={"POST"})
     *
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function postMessageAction(Request $request)
    {
        $this->connection->beginTransaction();

        try {
            list($messageData, $errors) = $this->messageService->getMessageData($request);

            if (!empty($errors)) {
                return View::create($errors, Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userService->createNewUser($messageData);
            $message = $this->messageService->createNewMessage($messageData, $user);

            $this->connection->commit();

            return $this->messageService->getCreatedView($message);
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    /**
     * @Route(
     *     "/messages/{messageId}",
     *     name="delete_messages",
     *     methods={"DELETE"},
     *     requirements={"message_id" ="\d+"}
     *     )
     *
     * @param Request $request
     * @param int $messageId
     *
     * @return View
     * @throws \Exception
     */
    public function deleteMessageAction(Request $request, $messageId)
    {
        $this->connection->beginTransaction();

        try {
            $message = $this->messageService->getMessageById($messageId);
            if (is_null($message)) {
                throw new NotFoundHttpException('This Message Is Not Found!');
            }

            $this->messageService->deleteMessage($message);

            $this->connection->commit();

            return $this->messageService->getDeletedView();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}