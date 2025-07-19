<?php
// src/Controller/AuthController.php
namespace App\Controller;

use App\Schemas\UsersSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\JWTService;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private DrizzleService $drizzleService,
        private JWTService $jwtService
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
        
        $token = $this->jwtService->createToken(UsersSchema::fromArray($user));
        return $this->json(['token' => $token]);
    }
}
