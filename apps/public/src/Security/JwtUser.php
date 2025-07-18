<?php
// src/Security/JwtUser.php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class JwtUser implements UserInterface
{
    private string $userIdentifier;

    public function __construct(private int $id, private string $email)
    {
        $this->userIdentifier = $email;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->userIdentifier;
    }

    public function eraseCredentials(): void
    {
        // nothing to erase
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
