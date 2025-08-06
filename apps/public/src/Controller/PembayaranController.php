<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Model\PembayaranEntryForm;
use App\Model\PembayaranUpdateForm;
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
/**
 * Description of PembayaranController
 *
 * @author aldo
 */

#[Route('/api')]
class PembayaranController extends AbstractController {

    public function __construct(
            private DrizzleService $drizzleService
    ) {
        
    }
     #[Route('/pembayarans/{id}', name: 'get_pembayaran', methods: ['GET'])]
    public function get(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

         $pembayaran = $db->select(PembayaranSchema::class)            
            ->where('id', '=', $id)
                ->first();

        return $this->json([
                    'pembayaran' => $pembayaran,
        ]);
    }

    #[Route('/pembayarans', name: 'all_pembayarans', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();
        $query = $db->select(PembayaranSchema::class)
            ->select([
                'pembayaran.*',
                'pelanggan.nama AS nama_pelanggan',
                'users.name AS nama_petugas',
                'tagihan.bulan_tahun',
            ])
            ->join(
                PelangganSchema::getTableName(),
                PelangganSchema::class,
                'pembayaran.pelanggan_id',
                '=',
                'pelanggan.id'
            )
            ->leftJoin(
                UsersSchema::getTableName(),
                UsersSchema::class,
                'pembayaran.petugas_id',
                '=',
                'users.id'
            )
            ->join(
                TagihanSchema::getTableName(),
                TagihanSchema::class,
                'pembayaran.tagihan_id',
                '=',
                'tagihan.id'
            )
            ->orderBy('pembayaran.tanggal_pembayaran', 'ASC');
        $pembayarans = $query->get();
        $sql = $query->getLastSql();
        return $this->json([
                    'pembayarans' => $pembayarans,
            'sql'=>$sql
        ]);
    }
    
    #[Route('/pembayarans', name: 'create_pembayaran', methods: ['POST'])]
    public function create(Request $request, TokenStorageInterface $tokenStorage, #[MapRequestPayload] PembayaranEntryForm $newPembayaran): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();
        
        $db = $this->drizzleService->getDb();

        $existing = $db->select(PembayaranSchema::class)
                ->where('pelanggan_id', '=', $newPembayaran->pelanggan_id)
                ->where('tagihan_id', '=', $newPembayaran->tagihan_id)
                ->first();
            

        if ($existing) {
            return $this->json(['error' => 'Pembayaran pelanggan sudah ada'], 409);
        }
        $tagihan = $db->select(TagihanSchema::class)
                ->where('id', '=', $newPembayaran->tagihan_id)                
                ->where('pelanggan_id', '=', $newPembayaran->pelanggan_id)
                ->first();
        if(!$tagihan){
            return $this->json(['error' => 'Tagihan not found'], 401);
        }
        // Insert with validation
        $ret = $db->insert(PembayaranSchema::class)
                ->values([
                    'pelanggan_id' => $newPembayaran->pelanggan_id,
                    'tagihan_id' => $newPembayaran->tagihan_id,
                    'jumlah' => $tagihan['jumlah'],
                    'petugas_id' => $user->id,
                    'tanggal_pembayaran' => empty($newPembayaran->tanggal_pembayaran) ? NULL : $newPembayaran->tanggal_pembayaran,
                    'metode_pembayaran' => empty($newPembayaran->metode_pembayaran) ? "" : $newPembayaran->metode_pembayaran,                    
                ])
                ->execute();
        if($ret){
            $newdata = ['status'=>'dibayar'];
            // Update with validation
            $ret = $db->update(TagihanSchema::class)
            ->set($newdata)                
             ->where('id','=',$newPembayaran->tagihan_id)
             ->execute();
        }
        //$token = $this->jwtService->createToken(PelangganSchema::fromArray($pelanggan));
        return $this->json(['success' => $ret, 'pembayaran'=>$newPembayaran]);
    }
    
    #[Route('/pembayarans/{id}', name: 'update_pembayaran', methods: ['PUT'])]
    public function update(Request $request, TokenStorageInterface $tokenStorage,  int $id, #[MapRequestPayload] PembayaranUpdateForm $existingPembayaran): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();

        $db = $this->drizzleService->getDb();

        $pembayaranArr1 = $db->select(PembayaranSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$pembayaranArr1) {
            return $this->json(['error' => 'Pembayaran does not exist'], 404);
        }
        $tagihan = $db->select(TagihanSchema::class)
                ->where('id', '=', $newPembayaran->tagihan_id)                
                ->where('pelanggan_id', '=', $newPembayaran->pelanggan_id)
                ->first();
        if(!$tagihan){
            return $this->json(['error' => 'Tagihan not found'], 401);
        }
        
         $newdata = ['jumlah'=>$tagihan['jumlah'],'metode_pembayaran'=>$existingPembayaran->metode_pembayaran,
             'tanggal_pembayaran'=>$existingPembayaran->tanggal_pembayaran];
        // Update with validation
        $ret = $db->update(PembayaranSchema::class)
        ->set($newdata)                
         ->where('id','=',$id)
         ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }
    
    #[Route('/pembayarans/{id}', name: 'delete_pembayaran', methods: ['DELETE'])]
    public function delete(Request $request, TokenStorageInterface $tokenStorage,  int $id): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();

        $db = $this->drizzleService->getDb();

        $paketArr1 = $db->select(PembayaranSchema::class)
                ->where('id', '=', $id)
                ->first();
        
        if (!$paketArr1) {
            return $this->json(['error' => 'Pembayaran does not exist'], 404);
        }
        $ret = $db->delete(PembayaranSchema::class)
        ->where('id','=',$id)
        ->execute();
        return $this->json([
                    'status' => $ret,
        ]);
    }
}
