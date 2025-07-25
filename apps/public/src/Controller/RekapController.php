<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller;

use App\Schemas\PelangganSchema;
use App\Schemas\PembayaranSchema;
use App\Service\DrizzleService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
    #[Route('/rekaps/excel', name: 'rekap_excel', methods: ['GET'])]
    public function excel(Request $request, TokenStorageInterface $tokenStorage): BinaryFileResponse|JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();
        $filter = $request->get('filter');
        $startDate = $request->get('start', '');
        $endDate = $request->get('end', '');
        if($filter=='range'){
            if(empty($startDate)|| empty($endDate)){
                return $this->json(['error' => 'Masukkan tanggal mulai dan akhir'], 401);
            }
        }
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
            case 'range':
                $builder->whereRaw("DATE(pembayaran.tanggal_pembayaran) between '$startDate' AND '$endDate'");
                break;
        }

        $results = $builder->get();
        $sql = $builder->getLastSql();
        // Generate Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray(['ID', 'Nama Pelanggan', 'Alamat', 'Jumlah', 'Metode Pembayaran', 'Tanggal Pembayaran'], null, 'A1');

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($results as $data) {
            $sheet->setCellValue("A$row", $data['id']);
            $sheet->setCellValue("B$row", $data['pelanggan']);
            $sheet->setCellValue("C$row", $data['alamat']);
            $sheet->setCellValue("D$row", $data['jumlah']);
            $sheet->setCellValue("E$row", $data['metode_pembayaran']);
            $sheet->setCellValue("F$row", $data['tanggal_pembayaran']);
            $row++;
            $no++;
        }

        // Temp file with .xlsx extension
        $tempFile = tempnam(sys_get_temp_dir(), 'rekap_pembayaran_');
        $xlsxFile = $tempFile . '.xlsx'; // PhpSpreadsheet needs .xlsx extension

        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxFile); // Save full .xlsx file

        // Return file response
        return $this->file(
            $xlsxFile,
            'rekap_pembayaran.xlsx',
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );
    }
    
    #[Route('/rekaps', name: 'rekap', methods: ['GET'])]
    public function all(Request $request, TokenStorageInterface $tokenStorage): JsonResponse {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $paket = $token->getUser();
        $filter = $request->get('filter');
        $startDate = $request->get('start', '');
        $endDate = $request->get('end', '');
        
        if($filter=='range'){
            if(empty($startDate)|| empty($endDate)){
                return $this->json(['error' => 'Masukkan tanggal mulai dan akhir'], 401);
            }
        }
        
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
            case 'range':
                $builder->whereRaw("DATE(pembayaran.tanggal_pembayaran) between '$startDate' AND '$endDate'");
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
