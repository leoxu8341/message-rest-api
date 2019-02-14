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

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Form\MessagePostForm;
use AppBundle\Interfaces\MessageRepositoryInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

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
     * @param PaginatorInterface $paginator
     * @param MessageRepositoryInterface $messageRepository
     * @param FormService $formService
     */
    public function __construct(
        PaginatorInterface $paginator,
        MessageRepositoryInterface $messageRepository,
        FormService $formService
    ){
        $this->paginator = $paginator;
        $this->messageRepository = $messageRepository;
        $this->formService = $formService;
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @param User $user | null
     *
     * @return QueryBuilder
     */
    public function getMessages(ParamFetcherInterface $paramFetcher, User $user = null): QueryBuilder
    {
        $sort = $paramFetcher->get('sorting');
        $query = $this->messageRepository->findMessages($sort, $user);

        return $query;
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
     * @param User $user
     * @param int $messageId
     * @return Message|null
     */
    public function getUserMessageById(User $user, int $messageId): ?Message
    {
        return $this->messageRepository->findByUserAndId($user, $messageId);
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
     * @param Request $request
     * @return Message
     */
    public function getMessageData(Request $request)
    {
        $message = new Message();

        $this->formService->postForm(
            json_decode($request->getContent(), true),
            $message,
            MessagePostForm::class
        );

        return $message;
    }

    /**
     * @param Message $message
     * @param User $user
     * @return Message
     */
    public function createNewMessage(
        $message,
        $user
    ) {
        $message->setUser($user);
        $this->storeMessage($message);

        return $message;
    }
}
