<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private const API_KEY = '5hhZODMRrfUCQRqrRvqQQlZiiTcijncQ';

    public function loadUserByIdentifier($identifier): UserInterface
    {
        if ($identifier === self::API_KEY) {
            return new ApiDummyUser();
        }

        throw new UserNotFoundException('API Key is not correct');
    }

    public function refreshUser(UserInterface $user)
    {
        return new ApiDummyUser();
    }

    public function supportsClass(string $class)
    {
        return true;
    }
}