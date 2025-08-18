<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Model\MassTagihanEntryForm;
use App\Model\TagihanEntryForm;
use App\Model\TagihanUpdateForm;
use App\Schemas\PaketSchema;
use App\Schemas\PelangganSchema;
use App\Schemas\TagihanSchema;
use App\Schemas\UsersSchema;
use App\Service\DrizzleService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
    
    
   #[Route('/tagihans/excel', name: 'excel_tagihans', methods: ['GET'])]
    public function excel(Request $request, TokenStorageInterface $tokenStorage): BinaryFileResponse
    {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $status = $request->query->get('status');
        $bulan_tahun = $request->query->get('bulan_tahun');

        $db = $this->drizzleService->getDb();
        $tagihans = $db->select(TagihanSchema::class)
            ->select([
                'tagihan.*',
                'pelanggan.nama AS pelanggan',
                'users.name AS petugas',
            ])
            ->join(
                PelangganSchema::getTableName(),
                PelangganSchema::class,
                'tagihan.pelanggan_id',
                '=',
                'pelanggan.id'
            )
            ->leftJoin(
                UsersSchema::getTableName(),
                UsersSchema::class,
                'tagihan.petugas_id',
                '=',
                'users.id'
            );

        if (!empty($status)) {
            $tagihans->where('tagihan.status', '=', $status);
        }

        if (!empty($bulan_tahun)) {
            $tagihans->where('tagihan.bulan_tahun', '=', $bulan_tahun);
        }

        $results = $tagihans->orderBy('tagihan.created_at', 'DESC')->get();

        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray(['No', 'Pelanggan', 'Jumlah', 'Status', 'Bulan/Tahun', 'Petugas'], null, 'A1');

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($results as $data) {
            $sheet->setCellValue("A$row", $no);
            $sheet->setCellValue("B$row", $data['pelanggan']);
            $sheet->setCellValue("C$row", $data['jumlah']);
            $sheet->setCellValue("D$row", $data['status'] === 'belum bayar' ? 'Belum Bayar' : 'Lunas');
            $sheet->setCellValue("E$row", $data['bulan_tahun']);
            $sheet->setCellValue("F$row", $data['petugas']);
            $row++;
            $no++;
        }

        // Temp file with .xlsx extension
        $tempFile = tempnam(sys_get_temp_dir(), 'tagihan_');
        $xlsxFile = $tempFile . '.xlsx'; // PhpSpreadsheet needs .xlsx extension

        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxFile); // Save full .xlsx file

        // Return file response
        return $this->file(
            $xlsxFile,
            'tagihan-semua.xlsx',
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );
    }
    
    #[Route('/tagihans/{id}', name: 'get_tagihan', methods: ['GET'])]
    public function get(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

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
            )
            ->where('tagihan.id', '=', $id)
                ->first();

        return $this->json([
                    'tagihan' => $tagihans,
        ]);
    }

    #[Route('/tagihans', name: 'all_tagihans', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }
        $status = $request->query->get('status');
        $bulan_tahun = $request->query->get('bulan_tahun');
        
        $tagihan = $token->getUser();

        $db = $this->drizzleService->getDb();

        $tagihans = $db->select(TagihanSchema::class)
            ->select([
                'tagihan.*',
                'pelanggan.nama AS pelanggan',
                'pelanggan.alamat AS alamat',
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
            'paket.nama AS tagihan_nama',
            'paket.harga AS tagihan_harga',
        ])
        ->leftJoin(
            PaketSchema::getTableName(),   // 'tagihan'
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
                                'jumlah' => $pelanggan['tagihan_harga'],
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
    
    #[Route('/tagihans/by-pelanggan/{pelanggan_id}', name: 'get_tagihan_pelanggan', methods: ['get'])]
    public function getTagihanPelanggan(Request $request, TokenStorageInterface $tokenStorage,  int $pelanggan_id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

        $tagihanList = $db->select(TagihanSchema::class)
        ->select(['id', 'bulan_tahun', 'jumlah'])
        ->where('pelanggan_id', '=', $pelanggan_id)
        ->whereIn('status', ['belum bayar', ''])
        ->get();

        return $this->json(['success' => 1, 'tagihans'=>$tagihanList]);
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
    
    #[Route('/tagihans/{id}', name: 'update_tagihan', methods: ['PUT'])]
    public function update(Request $request, TokenStorageInterface $tokenStorage,  int $id, #[MapRequestPayload] TagihanUpdateForm $existingTagihan): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $tagihan = $token->getUser();

        $db = $this->drizzleService->getDb();

        $tagihanArr1 = $db->select(TagihanSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$tagihanArr1) {
            return $this->json(['error' => 'Tagihan does not exist'], 404);
        }
        $tagihan1 = TagihanSchema::fromArray($tagihanArr1);
        
        $newdata = ['status'=>$existingTagihan->status,'metode_pembayaran'=>$existingTagihan->metode_pembayaran];
        if(!empty($existingTagihan->tanggal_bayar)){
            $newdata['tanggal_bayar'] = $existingTagihan->tanggal_bayar;
        }
        // Update with validation
        $ret = $db->update(TagihanSchema::class)
        ->set($newdata)                
         ->where('id','=',$id)
         ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }
    
    #[Route('/tagihans/{id}', name: 'delete_tagihan', methods: ['DELETE'])]
    public function delete(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

        $paketArr1 = $db->select(TagihanSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$paketArr1) {
            return $this->json(['error' => 'Tagihan does not exist'], 404);
        }
        $ret = $db->delete(TagihanSchema::class)
        ->where('id','=',$id)
        ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }
}
