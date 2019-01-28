<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 1/26/19
 * Time: 3:58 PM
 */
namespace AppBundle\Interfaces;

use AppBundle\Entity\User;

/**
 * Interface UserRepositoryInterface
 * @package AppBundle\Interfaces
 */
interface UserRepositoryInterface
{
    public function findById(int $userId): ?User;

    public function findByEmail(string $email): ?User;

    public function findAllUsers(): array;

    public function save(User $user): void;
}