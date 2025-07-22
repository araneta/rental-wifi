<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Model\MassTagihanEntryForm;
use App\Model\TagihanEntryForm;
use App\Schemas\PaketSchema;
use App\Schemas\PelangganSchema;
use App\Schemas\TagihanSchema;
use App\Schemas\UsersSchema;
use App\Service\DrizzleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use function Webmozart\Assert\Tests\StaticAnalysis\count;
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
            ->leftJoin(
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
            'status'=>$status,
            '$bulan_tahun'=>$bulan_tahun,
        ]);
    }
    
    #[Route('/tagihans/mass', name: 'create_mass_tagihan', methods: ['POST'])]
    public function createMass(#[MapRequestPayload] MassTagihanEntryForm $newTagihan): JsonResponse {
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
        ->where('pelanggan.status', '=', 'aktif')
        ->get();
        if(\count($pelanggans)>0){
            
            $tagihan_dibuat = 0;
            
            foreach($pelanggans as $pelanggan){
                $existing = $db->select(TagihanSchema::class)
                ->where('pelanggan_id', '=', $pelanggan['id'])
                ->where('bulan_tahun', '=', $newTagihan->bulan_tahun)
                ->first();
                if(!$existing){
                    // Insert with validation
                    $ret = $db->insert(TagihanSchema::class)
                            ->values([
                                'pelanggan_id' => $pelanggan['id'],
                                'bulan_tahun' => $newTagihan->bulan_tahun,
                                'jumlah' => $pelanggan['paket_harga'],
                                'status' => 'belum bayar',
                                //'tanggal_bayar' => $newTagihan->tanggal_bayar,
                                //'metode_pembayaran' => '',
                                'petugas_id' => $newTagihan->petugas_id,

                            ])
                            ->execute();
                    if($ret){
                        $tagihan_dibuat++;
                    }
                }
            }
            if($tagihan_dibuat>0){
                return $this->json(['success' => 1, 'count'=>$tagihan_dibuat,'message'=>'Tagihan untuk bulan '.$newTagihan->bulan_tahun.' berhasil ditambahkan sebanyak '.$tagihan_dibuat.' pelanggan.']);
            }else{
                return $this->json(['success' => 1, 'count'=>$tagihan_dibuat, 'message'=>'Semua pelanggan sudah memiliki tagihan untuk bulan '.$newTagihan->bulan_tahun]);
            }
        }else{
            return $this->json(['error' => 'Tidak ada pelanggan aktif ditemukan.'], 409);
        }


        //$token = $this->jwtService->createToken(PelangganSchema::fromArray($pelanggan));
        
    }
    
    #[Route('/tagihans', name: 'create_tagihan', methods: ['POST'])]
    public function create(#[MapRequestPayload] TagihanEntryForm $newTagihan): JsonResponse {
        $db = $this->drizzleService->getDb();

        $existing = $db->select(TagihanSchema::class)
                ->where('pelanggan_id', '=', $newTagihan->pelanggan_id)
                ->where('bulan_tahun', '=', $newTagihan->bulan_tahun)
                ->first();
            

        if ($existing) {
            return $this->json(['error' => 'Tagihan pelanggan sudah ada'], 409);
        }

        // Insert with validation
        $ret = $db->insert(TagihanSchema::class)
                ->values([
                    'pelanggan_id' => $newTagihan->pelanggan_id,
                    'bulan_tahun' => $newTagihan->bulan_tahun,
                    'jumlah' => $newTagihan->jumlah,
                    'status' => $newTagihan->status,
                    'tanggal_bayar' => empty($newTagihan->tanggal_bayar) ? NULL : $newTagihan->tanggal_bayar,
                    'metode_pembayaran' => empty($newTagihan->metode_pembayaran) ? "" : $newTagihan->metode_pembayaran,
                    'petugas_id' => $newTagihan->petugas_id,
                    
                ])
                ->execute();

        //$token = $this->jwtService->createToken(PelangganSchema::fromArray($pelanggan));
        return $this->json(['success' => $ret, 'tagihan'=>$newTagihan]);
    }
}
