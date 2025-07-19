<?php

namespace App\Controller;

use App\Model\UserEntryForm;
use App\Schemas\PelangganSchema;
use App\Schemas\PembayaranSchema;
use App\Schemas\TagihanSchema;
use App\Schemas\UsersSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/api')]
final class UserController extends AbstractController {

    public function __construct(
            private DrizzleService $drizzleService
    ) {
        
    }

    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    public function me(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
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

    #[Route('/analytics', name: 'analytics', methods: ['GET'])]
    public function analytics(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();

        $db = $this->drizzleService->getDb();

        $totalUsers = $db->select(UsersSchema::class)
                ->count();

        $totalPelanggan = $db->select(PelangganSchema::class)
                ->count();
        //
        $totalTagihan = $db->select(TagihanSchema::class)
                ->where('status', '=', 'belum bayar')
                ->count();

        $totalPembayaran = $db->select(PembayaranSchema::class)
                ->count();

        return $this->json([
                    'totalUsers' => $totalUsers,
                    'totalPelanggan' => $totalPelanggan,
                    'totalTagihan' => $totalTagihan,
                    'totalPembayaran' => $totalPembayaran,
        ]);
    }

    #[Route('/users', name: 'all_users', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();

        $db = $this->drizzleService->getDb();

        $users = $db->select(UsersSchema::class)
                ->get();

        return $this->json([
                    'users' => $users,
        ]);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function create(#[MapRequestPayload] UserEntryForm $newUser): JsonResponse {
        $db = $this->drizzleService->getDb();

        $user = $db->select(UsersSchema::class)
                ->where('email', '=', $newUser->email)
                ->first();

        if ($user) {
            return $this->json(['error' => 'Email already exist'], 401);
        }

        // Insert with validation
        $ret = $db->insert(UsersSchema::class)
                ->values([
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'role' => $newUser->role,
                    'password' => password_hash($newUser->password, PASSWORD_DEFAULT)
                ])
                ->execute();

        //$token = $this->jwtService->createToken(UsersSchema::fromArray($user));
        return $this->json(['success' => $ret, 'user'=>$newUser]);
    }
}
