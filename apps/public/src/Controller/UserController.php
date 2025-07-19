<?php

namespace App\Controller;

use App\Schemas\UsersSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api')]
final class UserController extends AbstractController
{
    public function __construct(
        private DrizzleService $drizzleService
    ) {}
    
    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    public function me(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();
        $claims = $token->getPayload();

        return $this->json([
            'user_id' => $claims['id'] ?? null,
            'email' => $claims['email'] ?? null
        ]);
    }
    
    #[Route('/analytics', name:'analytics', methods:['GET'])]
    public function analytics(Request $request, TokenStorageInterface $tokenStorage): JsonResponse{
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();
        
        
        $db = $this->drizzleService->getDb();

        $totalUsers = $db->select(UsersSchema::class)
            ->count();
        
        $totalPelanggan = $db->select(UsersSchema::class)
            ->count();

        return $this->json([            
            'totalUsers' => $totalUsers,
            'totalPelanggan' => $totalPelanggan,
        ]);
    }
}
