<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Interfaces\MessageRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

/**
 * MessageRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
final class MessageRepository implements MessageRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ObjectRepository
     */
    private $objectRepository;

    /**
     * MessageRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(Message::class);
    }

    /**
     * @param int $messageId
     * @return Message|null
     */
    public function findById(int $messageId): ?Message
    {
        return $this->objectRepository->find($messageId);
    }

    /**
     * @param User $user
     * @param int $messageId
     * @return Message|null
     */
    public function findByUserAndId(User $user, int $messageId): ?Message
    {
        return $this->objectRepository->findOneBy(['id' => $messageId, 'user' => $user]);
    }

    /**
     * @param string $sort
     * @param User $user | null
     * @return QueryBuilder
     */
    public function findMessages(string $sort, User $user = null): QueryBuilder
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(Message::class, 'm');

        if ($user) {
            $query->where('m.user = :user')
                ->setParameter('user', $user);
        }

        $query->orderBy('m.createdAt', $sort);

        return $query;
    }

    /**
     * @param Message $message
     */
    public function save(Message $message): void
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    /**
     * @param Message $message
     */
    public function delete(Message $message): void
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }
}
