<?php

declare(strict_types=1);

namespace App\Schemas;

use DrizzlePHP\Attributes\Column;
use DrizzlePHP\Attributes\Table;
use DrizzlePHP\Schema\Schema;

#[Table('paket')]
class PaketSchema extends Schema
{
    #[Column('id', 'int', primary: true, autoIncrement: true)]
    public int $id;

    #[Column('nama', 'string')]
    public string $nama;

    #[Column('kecepatan', 'string')]
    public string $kecepatan;

    #[Column('harga', 'int')]
    public int $harga;

    #[Column('deskripsi', 'text', nullable: true)]
    public ?string $deskripsi;

    #[Column('created_at', 'datetime')]
    public string $createdAt;

    public static function fromArray(array $data): PaketSchema
    {
        $paket = new PaketSchema();
        $paket->id = $data['id'] ?? 0;
        $paket->nama = $data['nama'] ?? '';
        $paket->kecepatan = $data['kecepatan'] ?? '';
        $paket->harga = $data['harga'] ?? 0;
        $paket->deskripsi = $data['deskripsi'] ?? null;
        $paket->createdAt = $data['created_at'] ?? '';

        return $paket;
    }
}
