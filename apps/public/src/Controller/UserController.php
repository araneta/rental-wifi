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
        $role = $request->query->get('role');
        $db = $this->drizzleService->getDb();

        $users = $db->select(UsersSchema::class);
        if (!empty($role)) {
            $users->where('role', '=', $role);
        }
        $result = $users->get();

        return $this->json([
                    'users' => $result,
        ]);
    }
    
    #[Route('/users/{id}', name: 'get_user', methods: ['GET'])]
    public function get(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();

        $db = $this->drizzleService->getDb();

        $user = $db->select(UsersSchema::class)
                ->where('id', '=', $id)
                ->first();

        return $this->json([
                    'user' => $user,
        ]);
    }
    #[Route('/users/{id}', name: 'update_user', methods: ['PUT'])]
    public function update(Request $request, TokenStorageInterface $tokenStorage,  int $id, #[MapRequestPayload] UserEntryForm $existingUser): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();

        $db = $this->drizzleService->getDb();

        $userArr1 = $db->select(UsersSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$userArr1) {
            return $this->json(['error' => 'User does not exist'], 404);
        }
        $user1 = UsersSchema::fromArray($userArr1);
        
        $userArr2 = $db->select(UsersSchema::class)
                ->where('email', '=', $existingUser->email)
                ->first();
        $user2 = UsersSchema::fromArray($userArr2);
        if ($user2 && $user2->id != $user1->id) {
            return $this->json(['error' => 'Email already exist'], 409);
        }
        $pass = $user1->password;
        if(!empty($existingUser->password)){
            $pass = password_hash($existingUser->password, PASSWORD_DEFAULT);
        }
        // Update with validation
        $ret = $db->update(UsersSchema::class)
        ->set(['name'=>$existingUser->name,'email'=>$existingUser->email,'role'=>$existingUser->role, 'password'=> $pass])                
         ->where('id','=',$id)
         ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function create(#[MapRequestPayload] UserEntryForm $newUser): JsonResponse {
        $db = $this->drizzleService->getDb();

        $user = $db->select(UsersSchema::class)
                ->where('email', '=', $newUser->email)
                ->first();

        if ($user) {
            return $this->json(['error' => 'Email already exist'], 409);
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
