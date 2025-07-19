<?php

// src/Security/UserProvider.php
namespace App\Security;

use App\Schemas\UsersSchema;
use App\Service\DrizzleService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private DrizzleService $drizzleService,) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $db = $this->drizzleService->getDb();

        $user = $db->select(UsersSchema::class)
            ->where('email', '=', $identifier)            
            ->first();
        
        

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
        }

        return UsersSchema::fromArray($user);
    }

    // For older Symfony versions (pre-5.3)
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UsersSchema) {
            throw new UnsupportedUserException('Unsupported user type');
        }
        
        $db = $this->drizzleService->getDb();

        $user = $db->select(UsersSchema::class)
            ->where('email', '=', $user->getUserIdentifier())            
            ->first();

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === UsersSchema::class;
    }
}
