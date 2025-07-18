<?php
// src/Controller/AuthController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DrizzleService;
use Firebase\JWT\JWT;
use App\Schemas\UsersSchema;

class LoginController extends AbstractController
{
    public function __construct(
        private DrizzleService $drizzleService
    ) {}

    #[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email and password required'], 400);
        }

        $db = $this->drizzleService->getDb();

        $user = $db->select(UsersSchema::class)
            ->where('email', '=', $email)
            ->where('password', '=', $password) // ❗ change to password_verify() if using hashes
            ->first();

        if (!$user) {
            return $this->json(['error' => 'Invalid credentialsx'], 401);
        }

        $payload = [
            'id' => $user['id'],
            'email' => $user['email'],
            'exp' => time() + 3600
        ];

        $jwtSecret = $_ENV['JWT_SECRET'] ?? 'your_jwt_secret';
        $token = JWT::encode($payload, $jwtSecret, 'HS256');

        return $this->json(['token' => $token]);
    }
}
