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
    public int $pelanggan_id;

    #[Column('bulan_tahun', 'string')]
    public string $bulan_tahun; // Format 'YYYY-MM'

    #[Column('jumlah', 'decimal')]
    public float $jumlah;

    #[Column('status', 'string')]
    public string $status;

    #[Column('tanggal_bayar', 'date', nullable: true)]
    public ?string $tanggal_bayar;

    #[Column('metode_pembayaran', 'string', nullable: true)]
    public ?string $metode_pembayaran;

    #[Column('petugas_id', 'int', nullable: true)]
    public ?int $petugas_id;

    #[Column('created_at', 'datetime')]
    public string $created_at;

    public static function fromArray(array $data): TagihanSchema
    {
        $tagihan = new TagihanSchema();
        $tagihan->id = $data['id'] ?? 0;
        $tagihan->pelanggan_id = $data['pelanggan_id'] ?? 0;
        $tagihan->bulan_tahun = $data['bulan_tahun'] ?? '';
        $tagihan->jumlah = isset($data['jumlah']) ? (float)$data['jumlah'] : 0.0;
        $tagihan->status = $data['status'] ?? 'belum bayar';
        $tagihan->tanggalBayar = $data['tanggal_bayar'] ?? null;
        $tagihan->metode_pembayaran = $data['metode_pembayaran'] ?? null;
        $tagihan->petugas_id = isset($data['petugas_id']) ? (int)$data['petugas_id'] : null;
        $tagihan->created_at = $data['created_at'] ?? '';

        return $tagihan;
    }
}
