<?php

declare(strict_types=1);

namespace App\Schemas;

use DrizzlePHP\Attributes\Column;
use DrizzlePHP\Attributes\Table;
use DrizzlePHP\Schema\Schema;
use Symfony\Component\Security\Core\User\UserInterface;

#[Table('users')]
class UsersSchema extends Schema implements UserInterface 
{
    #[Column('id', 'int', primary: true, autoIncrement: true)]
    public int $id;

    #[Column('name', 'string')]
    public string $name;

    #[Column('email', 'string')]
    public string $email;

    #[Column('password', 'string', nullable: true)]
    public string $password;

    #[Column('role', 'string')]
    public string $role;

    #[Column('created_at', 'datetime')]
    public string $createdAt;
    
    public function getUserIdentifier():string
    {
        return (string)$this->id;
    }

    public function getRoles(): array
    {
        // Symfony expects an array of roles, e.g., ['ROLE_USER']
        return [$this->role];
    }

    public function eraseCredentials(): void
    {
        // If storing any temporary sensitive data, clear it here
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Optional: for legacy support
    public function getUsername(): string
    {
        return $this->email;
    }
    
    public static function fromArray(?array $data): ?UsersSchema
    {
        if(empty($data)){
            return NULL;
        }
        $user = new UsersSchema();
        $user->id = $data['id'] ?? 0;
        $user->email = $data['email'] ?? '';
        $user->password = $data['password'] ?? null;
        $user->role = $data['role'] ?? 'ROLE_USER';
        $user->name = $data['name'] ?? '';
        $user->createdAt = $data['created_at'] ?? '';

        return $user;
    }
}
