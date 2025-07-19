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
    public int $tagihanId;

    #[Column('pelanggan_id', 'int')]
    public int $pelangganId;

    #[Column('petugas_id', 'int', nullable: true)]
    public ?int $petugasId;

    #[Column('jumlah', 'float')]
    public float $jumlah;

    #[Column('metode_pembayaran', 'string')]
    public string $metodePembayaran;

    #[Column('tanggal_pembayaran', 'datetime')]
    public string $tanggalPembayaran;

    public static function fromArray(array $data): PembayaranSchema
    {
        $pembayaran = new PembayaranSchema();
        $pembayaran->id = $data['id'] ?? 0;
        $pembayaran->tagihanId = $data['tagihan_id'] ?? 0;
        $pembayaran->pelangganId = $data['pelanggan_id'] ?? 0;
        $pembayaran->petugasId = $data['petugas_id'] ?? null;
        $pembayaran->jumlah = (float)($data['jumlah'] ?? 0);
        $pembayaran->metodePembayaran = $data['metode_pembayaran'] ?? '';
        $pembayaran->tanggalPembayaran = $data['tanggal_pembayaran'] ?? '';

        return $pembayaran;
    }
}
