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
use AppBundle\Interfaces\UserRepositoryInterface;
use AppBundle\Traits\ExceptionTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class LoginService
 *
 * @package AppBundle\Service
 */
final class LoginService
{
    use ExceptionTrait;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * LoginService constructor.
     * @param UserRepositoryInterface $userRepository
     * @param JWTEncoderInterface $encoder
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        JWTEncoderInterface $encoder,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param $loginData
     * @return string
     */
    public function login($loginData) {
        if (!isset($loginData['email']) ||
            !isset($loginData['password']) ||
            empty($loginData['email']) ||
            empty($loginData['password'])
        ) {
            $this->throwUnauthorizedException('Fields can not by empty');
        }

        $email = $loginData['email'];

        $user = $this->userRepository->findByEmail($email);

        if (is_null($user)) {
            $this->throwUnauthorizedException('User does not exist');
        }

        $valid = $this->passwordEncoder->isPasswordValid($user, $loginData['password']);
        if (!$valid) {
            $this->throwUnauthorizedException('Wrong Password');
        }

        return $this->getToken($user);
    }

    /**
     * @param $user
     * @return string
     * @throws JWTEncodeFailureException
     */
    private function getToken($user)
    {
        try {
            return $this->encoder->encode($this->getPayload($user));
        }
        catch ( JWTEncodeFailureException $e ) {
            throw $e;
        }
    }

    /**
     * @param User $user
     * @return array
     */
    private function getPayload(User $user)
    {
        return
            [
                'username'   => $user->getUsername(),
                'user_id'    => $user->getId(),
                'email'      => $user->getEmail()
            ];
    }
}
