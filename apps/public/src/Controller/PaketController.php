<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Model\PaketEntryForm;
use App\Schemas\PaketSchema;
use App\Schemas\UserSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
/**
 * Description of PaketController
 *
 * @author aldo
 */

#[Route('/api')]
class PaketController extends AbstractController {

    public function __construct(
            private DrizzleService $drizzleService
    ) {
        
    }
    
    #[Route('/pakets', name: 'all_pakets', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

        $pakets = $db->select(PaketSchema::class)
                ->get();

        return $this->json([
                    'pakets' => $pakets,
        ]);
    }
    
    #[Route('/pakets', name: 'create_paket', methods: ['POST'])]
    public function create(#[MapRequestPayload] PaketEntryForm $newPaket): JsonResponse {
        $db = $this->drizzleService->getDb();

        $existing = $db->select(PaketSchema::class)
                ->where('nama', '=', $newPaket->nama)
                ->first();

        if ($existing) {
            return $this->json(['error' => 'Nama paket sudah ada'], 409);
        }

        // Insert with validation
        $ret = $db->insert(PaketSchema::class)
                ->values([
                    'nama' => $newPaket->nama,
                    'kecepatan' => $newPaket->kecepatan,
                    'harga' => $newPaket->harga,
                    'deskripsi' => $newPaket->deskripsi,
                ])
                ->execute();

        //$token = $this->jwtService->createToken(PaketSchema::fromArray($paket));
        return $this->json(['success' => $ret, 'paket'=>$newPaket]);
    }
    
    #[Route('/pakets/{id}', name: 'get_paket', methods: ['GET'])]
    public function get(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

        $paket = $db->select(PaketSchema::class)
                ->where('id', '=', $id)
                ->first();

        return $this->json([
                    'paket' => $paket,
        ]);
    }
    #[Route('/pakets/{id}', name: 'update_paket', methods: ['PUT'])]
    public function update(Request $request, TokenStorageInterface $tokenStorage,  int $id, #[MapRequestPayload] UserEntryForm $existingPaket): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

        $paketArr1 = $db->select(PaketSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$paketArr1) {
            return $this->json(['error' => 'Paket does not exist'], 404);
        }
        $paket1 = PaketSchema::fromArray($paketArr1);
        
        $paketArr2 = $db->select(PaketSchema::class)
                ->where('nama', '=', $existingPaket->nama)
                ->first();
        $paket2 = PaketSchema::fromArray($paketArr2);
        if ($paket2 && $paket2->id != $paket1->id) {
            return $this->json(['error' => 'Email already exist'], 409);
        }
        $pass = $paket1->password;
        if(!empty($existingPaket->password)){
            $pass = password_hash($existingPaket->password, PASSWORD_DEFAULT);
        }
        // Update with validation
        $ret = $db->update(PaketSchema::class)
        ->set(['name'=>$existingPaket->name,'email'=>$existingPaket->email,'role'=>$existingPaket->role, 'password'=> $pass])                
         ->where('id','=',$id)
         ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }
}

    
    
