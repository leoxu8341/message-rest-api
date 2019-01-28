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
use AppBundle\Entity\User;
use AppBundle\Interfaces\UserRepositoryInterface;

/**
 * Class UserService
 *
 * @package AppBundle\Service
 */
final class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     * @return User|null
     */
    public function getUser(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail($email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->userRepository->findAllUsers();
    }

    /**
     * @param User $user
     */
    public function storeUser(User $user): void
    {
        $this->userRepository->save($user);
    }


    /**
     * @param MessageAndUserData $userData
     * @return User
     */
    public function createNewUser(
        $userData
    ) {
        $name = $userData->getName();
        $email = $userData->getEmail();

        $user = $this->getUserByEmail($email);

        if (is_null($user)) {
            $user = new User();

            $user->setEmail($email);
        }

        $user->setUsername($name);

        $this->storeUser($user);

        return $user;
    }
}
