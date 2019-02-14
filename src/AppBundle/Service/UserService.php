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

use AppBundle\Entity\User;
use AppBundle\Form\UserPostForm;
use AppBundle\Interfaces\UserRepositoryInterface;
use AppBundle\Traits\ExceptionTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService
 *
 * @package AppBundle\Service
 */
final class UserService
{
    use ExceptionTrait;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var FormService
     */
    private $formService;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepository
     * @param FormService $formService
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        FormService $formService,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->formService = $formService;
        $this->passwordEncoder = $passwordEncoder;
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
     * @param string $username
     * @return User|null
     */
    public function getUserByUsername($username): ?User
    {
        return $this->userRepository->findByUsername($username);
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
        $emailUser = $this->getUserByEmail($user->getEmail());
        if ($emailUser) {
            $this->throwConflicException('User with email: '.$user->getEmail().' already exists');
        }

        $nameUser = $this->getUserByUsername($user->getUsername());
        if ($nameUser) {
            $this->throwConflicException('User with username: '.$user->getUsername().' already exists');
        }

        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->userRepository->save($user);
    }

    /**
     * @param Request $request
     * @return User
     */
    public function createNewUser(Request $request) : User
    {
        $user = new User();

        $this->formService->postForm(
            json_decode($request->getContent(), true),
            $user,
            UserPostForm::class
        );

        return $user;
    }
}
