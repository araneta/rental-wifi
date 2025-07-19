<?php

namespace App\Service;

use App\Schemas\UsersSchema;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private string $jwtSecret;

    public function __construct(string $jwtSecret)
    {
        $this->jwtSecret = $jwtSecret;
    }

    public function createToken(UsersSchema $user): string
    {
        $payload = [
            'id' => $user->id,
            'email'=>$user->email,
            'roles' => $user->getRoles(),
            'exp' => time() + 3600, // Token expires in 1 hour
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function decodeToken(string $token): ?array
    {
        try {
            return (array) JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
        } catch (Exception $e) {
            return null;
        }
    }
}
