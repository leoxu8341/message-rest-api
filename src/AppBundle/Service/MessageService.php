<?php
/**
 * Copyright (c) 2019.
 *  This file is subject to the terms and conditions defined in file 'LICENSE.txt', which is part of this source code package.
 */

/**
 * Created by PhpStorm.
 * User: Programmer
 * Date: 1/26/2019
 * Time: 4:37 PM
 */

namespace AppBundle\Service;

use AppBundle\Data\MessageAndUserData;
use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Form\MessagePostForm;
use AppBundle\Interfaces\MessageRepositoryInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\QueryBuilder;

/**
 * Class MessageService
 *
 * @package AppBundle\Service
 */
class MessageService
{
    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var FormService
     */
    private $formService;

    /**
     * MessageService constructor.
     * @param MessageRepositoryInterface $messageRepository
     * @param PaginatorInterface $paginator
     * @param FormService $formService
     */
    public function __construct(
        MessageRepositoryInterface $messageRepository,
        PaginatorInterface $paginator,
        FormService $formService
    ){
        $this->messageRepository = $messageRepository;
        $this->paginator = $paginator;
        $this->formService = $formService;
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return QueryBuilder
     */
    public function getMessages(ParamFetcherInterface $paramFetcher): QueryBuilder
    {
        $sort = $paramFetcher->get('sorting');
        $query = $this->messageRepository->findMessages($sort);

        return $query;
    }

    /**
     * @param Message $message
     */
    public function storeMessage(Message $message): void
    {
        $this->messageRepository->save($message);
    }

    /**
     * @param Message $message
     */
    public function deleteMessage(?Message $message): void
    {
        if (!is_null($message)) {
            $this->messageRepository->delete($message);
        }
    }

    /**
     * @param int $messageId
     * @return Message|null
     */
    public function getMessageById(int $messageId): ?Message
    {
        return $this->messageRepository->findById($messageId);
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @param array $messages
     * @return View
     */
    public function getPaginatedView(
        ParamFetcherInterface $paramFetcher,
        $messages
    ) {
        $limit = $paramFetcher->get('page_limit');
        $index = $paramFetcher->get('page_index');

        $items = $this->paginator->paginate($messages, $index, $limit);

        return View::create($items, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getMessageData(Request $request)
    {
        $messageData = new MessageAndUserData();

        $errors = $this->formService->postForm(
            json_decode($request->getContent(), true),
            $messageData,
            MessagePostForm::class
        );

        return [$messageData, $errors];
    }

    /**
     * @param MessageAndUserData $messageData
     * @param User $user
     * @return Message
     */
    public function createNewMessage(
        $messageData,
        $user
    ) {
        $messageBody = $messageData->getMessage();

        $message = new Message();
        $message->setUser($user);
        $message->setMessageBody($messageBody);

        $this->storeMessage($message);

        return $message;
    }

    /**
     * @param Message $message
     * @return View
     */
    public function getCreatedView(
        $message
    ) {
        return View::create($message, Response::HTTP_CREATED);
    }

    /**
     * @return View
     */
    public function getDeletedView() {
        return View::create(null,Response::HTTP_OK);
    }
}
