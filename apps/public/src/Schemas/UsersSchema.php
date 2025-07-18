<?php

declare(strict_types=1);

namespace App\Schemas;

use DrizzlePHP\Schema\Schema;
use DrizzlePHP\Attributes\Table;
use DrizzlePHP\Attributes\Column;

#[Table('users')]
class UsersSchema extends Schema
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
}
