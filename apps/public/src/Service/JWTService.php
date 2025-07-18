<?php
// src/Service/JWTService.php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTService
{
    private string $jwtSecret = 'your_jwt_secret'; // Store in env later

    public function createToken(UserInterface $user): string
    {
        $payload = [
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'exp' => time() + 3600,
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function decodeToken(string $token): ?array
    {
        try {
            return (array) JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
