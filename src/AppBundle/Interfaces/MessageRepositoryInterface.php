<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 1/26/19
 * Time: 3:58 PM
 */
namespace AppBundle\Interfaces;

use AppBundle\Entity\Message;

/**
 * Interface MessageRepositoryInterface
 * @package AppBundle\Interfaces
 */
interface MessageRepositoryInterface
{
    public function findById(int $messageId): ?Message;

    public function findMessages($sort): array;

    public function save(Message $message): void;

    public function delete(Message $message): void;
}