<?php

declare(strict_types=1);

namespace App\Schemas;

use DrizzlePHP\Attributes\Column;
use DrizzlePHP\Attributes\Table;
use DrizzlePHP\Schema\Schema;

#[Table('pelanggan')]
class PelangganSchema extends Schema
{
    #[Column('id', 'int', primary: true, autoIncrement: true)]
    public int $id;

    #[Column('nama', 'string')]
    public string $nama;

    #[Column('alamat', 'text')]
    public string $alamat;

    #[Column('no_hp', 'text')]
    public string $noHp;

    #[Column('status', 'string', default: 'aktif')]
    public string $status;

    #[Column('created_at', 'datetime')]
    public string $createdAt;

    #[Column('paket_id', 'int', nullable: true)]
    public ?int $paketId;

    #[Column('pop', 'text')]
    public string $pop;

    public static function fromArray(array $data): PelangganSchema
    {
        $pelanggan = new PelangganSchema();
        $pelanggan->id = $data['id'] ?? 0;
        $pelanggan->nama = $data['nama'] ?? '';
        $pelanggan->alamat = $data['alamat'] ?? '';
        $pelanggan->noHp = $data['no_hp'] ?? '';
        $pelanggan->status = $data['status'] ?? 'aktif';
        $pelanggan->createdAt = $data['created_at'] ?? '';
        $pelanggan->paketId = isset($data['paket_id']) ? (int) $data['paket_id'] : null;
        $pelanggan->pop = $data['pop'] ?? '';

        return $pelanggan;
    }
}
