<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Schemas\PelangganSchema;
use App\Schemas\TagihanSchema;
use App\Schemas\UsersSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
/**
 * Description of TagihanController
 *
 * @author aldo
 */

#[Route('/api')]
class TagihanController extends AbstractController {

    public function __construct(
            private DrizzleService $drizzleService
    ) {
        
    }
    
    #[Route('/tagihans', name: 'all_tagihans', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }
        $status = $request->query->get('status');
        $bulan_tahun = $request->query->get('bulan_tahun');
        
        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

        $tagihans = $db->select(TagihanSchema::class)
            ->select([
                'tagihan.*',
                'pelanggan.nama AS pelanggan',
                'users.name AS petugas',
            ])
            ->join(
                PelangganSchema::getTableName(),   // 'pelanggan'
                PelangganSchema::class,
                'tagihan.pelanggan_id',
                '=',
                'pelanggan.id'
            )
            ->join(
                UsersSchema::getTableName(),   // 'users'
                UsersSchema::class,
                'tagihan.petugas_id',
                '=',
                'users.id'
            );

        // Optional filters
        if (!empty($status)) {
            $tagihans->where('tagihan.status', '=', $status);
        }

        if (!empty($bulan_tahun)) {
            $tagihans->where('tagihan.bulan_tahun', '=', $bulan_tahun);
        }

        $results = $tagihans
            ->orderBy('tagihan.created_at', 'DESC')
            ->get();


        return $this->json([
                    'tagihans' => $results,
        ]);
    }
}
