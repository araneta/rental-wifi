<?php

declare(strict_types=1);

namespace App\Schemas;

use DrizzlePHP\Attributes\Column;
use DrizzlePHP\Attributes\Table;
use DrizzlePHP\Schema\Schema;

#[Table('tagihan')]
class TagihanSchema extends Schema
{
    #[Column('id', 'int', primary: true, autoIncrement: true)]
    public int $id;

    #[Column('pelanggan_id', 'int')]
    public int $pelangganId;

    #[Column('bulan_tahun', 'string')]
    public string $bulanTahun; // Format 'YYYY-MM'

    #[Column('jumlah', 'decimal')]
    public float $jumlah;

    #[Column('status', 'string', default: 'belum bayar')]
    public string $status;

    #[Column('tanggal_bayar', 'date', nullable: true)]
    public ?string $tanggalBayar;

    #[Column('metode_pembayaran', 'string', nullable: true)]
    public ?string $metodePembayaran;

    #[Column('petugas_id', 'int', nullable: true)]
    public ?int $petugasId;

    #[Column('created_at', 'datetime')]
    public string $createdAt;

    public static function fromArray(array $data): TagihanSchema
    {
        $tagihan = new TagihanSchema();
        $tagihan->id = $data['id'] ?? 0;
        $tagihan->pelangganId = $data['pelanggan_id'] ?? 0;
        $tagihan->bulanTahun = $data['bulan_tahun'] ?? '';
        $tagihan->jumlah = isset($data['jumlah']) ? (float)$data['jumlah'] : 0.0;
        $tagihan->status = $data['status'] ?? 'belum bayar';
        $tagihan->tanggalBayar = $data['tanggal_bayar'] ?? null;
        $tagihan->metodePembayaran = $data['metode_pembayaran'] ?? null;
        $tagihan->petugasId = isset($data['petugas_id']) ? (int)$data['petugas_id'] : null;
        $tagihan->createdAt = $data['created_at'] ?? '';

        return $tagihan;
    }
}
