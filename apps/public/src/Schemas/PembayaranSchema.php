<?php

declare(strict_types=1);

namespace App\Schemas;

use DrizzlePHP\Attributes\Column;
use DrizzlePHP\Attributes\Table;
use DrizzlePHP\Schema\Schema;

#[Table('pembayaran')]
class PembayaranSchema extends Schema
{
    #[Column('id', 'int', primary: true, autoIncrement: true)]
    public int $id;

    #[Column('tagihan_id', 'int')]
    public int $tagihan_id;

    #[Column('pelanggan_id', 'int')]
    public int $pelanggan_id;

    #[Column('petugas_id', 'int', nullable: true)]
    public ?int $petugas_id;

    #[Column('jumlah', 'float')]
    public float $jumlah;

    #[Column('metode_pembayaran', 'string')]
    public string $metode_pembayaran;

    #[Column('tanggal_pembayaran', 'datetime')]
    public string $tanggal_pembayaran;

    public static function fromArray(array $data): PembayaranSchema
    {
        $pembayaran = new PembayaranSchema();
        $pembayaran->id = isset($data['id']) ? intval($data['id']) :  0;
        $pembayaran->tagihan_id = $data['tagihan_id'] ?? 0;
        $pembayaran->pelanggan_id = $data['pelanggan_id'] ?? 0;
        $pembayaran->petugas_id = $data['petugas_id'] ?? null;
        $pembayaran->jumlah = (float)($data['jumlah'] ?? 0);
        $pembayaran->metode_pembayaran = $data['metode_pembayaran'] ?? '';
        $pembayaran->tanggal_pembayaran = $data['tanggal_pembayaran'] ?? '';

        return $pembayaran;
    }
}
