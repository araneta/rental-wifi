<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Model\PaketEntryForm;
use App\Schemas\PaketSchema;
use App\Schemas\UsersSchema;
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

        $user = $token->getUser();

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

        //$token = $this->jwtService->createToken(UsersSchema::fromArray($user));
        return $this->json(['success' => $ret, 'paket'=>$newPaket]);
    }
}

    
    
