<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Schemas\PelangganSchema;
use App\Schemas\PembayaranSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of RekapController
 *
 * @author aldo
 */
#[Route('/api')]
class RekapController extends AbstractController {

    public function __construct(
            private DrizzleService $drizzleService
    ) {
        
    }

    //put your code here

    #[Route('/rekaps', name: 'rekap', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();
        $filter = $request->get('filter');

        $db = $this->drizzleService->getDb();
        $builder = $db->select(PembayaranSchema::class)
                ->select([
                    'pembayaran.id',
                    'pelanggan.nama AS pelanggan',
                    'pelanggan.alamat',
                    'pembayaran.jumlah',
                    'pembayaran.metode_pembayaran',
                    'pembayaran.tanggal_pembayaran',
                ])
                ->join(
                PelangganSchema::getTableName(),
                PelangganSchema::class,
                'pembayaran.pelanggan_id',
                '=',
                'pelanggan.id'
        );

        switch ($filter) {
            case 'hari':
                $builder->whereRaw('DATE(pembayaran.tanggal_pembayaran) = CURDATE()');
                break;
            case 'minggu':
                $builder->whereRaw('YEARWEEK(pembayaran.tanggal_pembayaran, 1) = YEARWEEK(CURDATE(), 1)');
                break;
            case 'bulan':
                $builder->whereRaw('MONTH(pembayaran.tanggal_pembayaran) = MONTH(CURDATE())')
                        ->whereRaw('YEAR(pembayaran.tanggal_pembayaran) = YEAR(CURDATE())');
                break;
            case 'tahun':
                $builder->whereRaw('YEAR(pembayaran.tanggal_pembayaran) = YEAR(CURDATE())');
                break;
        }

        $result = $builder->get();
        $sql = $builder->getLastSql();
        return $this->json([
                    'pembayarans' => $result,
                    'sql' => $sql
        ]);
    }
}
