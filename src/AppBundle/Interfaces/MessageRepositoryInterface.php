<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 1/26/19
 * Time: 3:58 PM
 */
namespace AppBundle\Interfaces;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;

/**
 * Interface MessageRepositoryInterface
 * @package AppBundle\Interfaces
 */
interface MessageRepositoryInterface
{
    public function findById(int $messageId): ?Message;

    public function findMessages(string $sort, User $user): QueryBuilder;

    public function save(Message $message): void;

    public function delete(Message $message): void;

    public function findByUserAndId(User $user, int $messageId): ?Message;
}