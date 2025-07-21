<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Model\PelangganEntryForm;
use App\Schemas\PaketSchema;
use App\Schemas\PelangganSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
/**
 * Description of PelangganController
 *
 * @author aldo
 */

#[Route('/api')]
class PelangganController extends AbstractController {

    public function __construct(
            private DrizzleService $drizzleService
    ) {
        
    }
    
    #[Route('/pelanggans', name: 'all_pelanggans', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $pelanggan = $token->getUser();

        $db = $this->drizzleService->getDb();

        $pelanggans = $db->select(PelangganSchema::class)
        ->select([
            'pelanggan.id',
            'pelanggan.nama',
            'pelanggan.no_hp',
            'paket.nama AS paket_nama',
            'paket.harga AS paket_harga',
        ])
        ->leftJoin(
            PaketSchema::getTableName(),   // 'paket'
            PaketSchema::class,
            'pelanggan.paket_id',
            '=',
            'paket.id'
        )
        ->get();
        return $this->json([
                    'pelanggans' => $pelanggans,
        ]);
    }
    
    #[Route('/pelanggans', name: 'create_pelanggan', methods: ['POST'])]
    public function create(#[MapRequestPayload] PelangganEntryForm $newPelanggan): JsonResponse {
        $db = $this->drizzleService->getDb();

        $existing = $db->select(PelangganSchema::class)
                ->where('nama', '=', $newPelanggan->nama)
                ->first();
            

        if ($existing) {
            return $this->json(['error' => 'Nama pelanggan sudah ada'], 409);
        }

        // Insert with validation
        $ret = $db->insert(PelangganSchema::class)
                ->values([
                    'nama' => $newPelanggan->nama,
                    'alamat' => $newPelanggan->alamat,
                    'no_hp' => $newPelanggan->no_hp,
                    'status' => $newPelanggan->status,
                    'paket_id' => $newPelanggan->paket_id,
                    'pop' => $newPelanggan->pop,
                    
                ])
                ->execute();

        //$token = $this->jwtService->createToken(PelangganSchema::fromArray($pelanggan));
        return $this->json(['success' => $ret, 'pelanggan'=>$newPelanggan]);
    }
    
    #[Route('/pelanggans/{id}', name: 'get_pelanggan', methods: ['GET'])]
    public function get(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $pelanggan = $token->getUser();

        $db = $this->drizzleService->getDb();

        $pelanggan = $db->select(PelangganSchema::class)
                ->where('id', '=', $id)
                ->first();

        return $this->json([
                    'pelanggan' => $pelanggan,
        ]);
    }
    #[Route('/pelanggans/{id}', name: 'update_pelanggan', methods: ['PUT'])]
    public function update(Request $request, TokenStorageInterface $tokenStorage,  int $id, #[MapRequestPayload] PelangganEntryForm $existingPelanggan): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $pelanggan = $token->getUser();

        $db = $this->drizzleService->getDb();

        $pelangganArr1 = $db->select(PelangganSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$pelangganArr1) {
            return $this->json(['error' => 'Pelanggan does not exist'], 404);
        }
        $pelanggan1 = PelangganSchema::fromArray($pelangganArr1);
        
        $pelangganArr2 = $db->select(PelangganSchema::class)
                ->where('nama', '=', $existingPelanggan->nama)
                ->first();
        $pelanggan2 = PelangganSchema::fromArray($pelangganArr2);
        if ($pelanggan2 && $pelanggan2->id != $pelanggan1->id) {
            return $this->json(['error' => 'Nama already exist'], 409);
        }
        
        // Update with validation
        $ret = $db->update(PelangganSchema::class)
        ->set(['nama'=>$existingPelanggan->nama,
            'alamat'=>$existingPelanggan->alamat,
            'no_hp'=>$existingPelanggan->no_hp, 
            'status'=> $existingPelanggan->status,
            'paket_id'=> $existingPelanggan->paket_id,
            'pop'=> $existingPelanggan->pop,
            ])                
         ->where('id','=',$id)
         ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }
    
    #[Route('/pelanggans/{id}', name: 'delete_pelanggan', methods: ['DELETE'])]
    public function delete(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $pelanggan = $token->getUser();

        $db = $this->drizzleService->getDb();

        $pelangganArr1 = $db->select(PelangganSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$pelangganArr1) {
            return $this->json(['error' => 'Pelanggan does not exist'], 404);
        }
        $ret = $db->delete(PelangganSchema::class)
        ->where('id','=',$id)
        ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }
}
