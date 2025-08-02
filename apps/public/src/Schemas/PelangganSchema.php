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
    public string $no_hp;

    #[Column('status', 'string')]
    public string $status;

    #[Column('created_at', 'datetime')]
    public string $createdAt;

    #[Column('paket_id', 'int', nullable: true)]
    public ?int $paket_id;

    #[Column('pop', 'text')]
    public string $pop;

    public static function fromArray(?array $data): ?PelangganSchema
    {
        if(empty($data)){
            return NULL;
        }
        $pelanggan = new PelangganSchema();
        $pelanggan->id = isset($data['id']) ? intval($data['id']) :  0;
        $pelanggan->nama = $data['nama'] ?? '';
        $pelanggan->alamat = $data['alamat'] ?? '';
        $pelanggan->no_hp = $data['no_hp'] ?? '';
        $pelanggan->status = $data['status'] ?? 'aktif';
        $pelanggan->createdAt = $data['created_at'] ?? '';
        $pelanggan->paket_id = isset($data['paket_id']) ? (int) $data['paket_id'] : null;
        $pelanggan->pop = $data['pop'] ?? '';

        return $pelanggan;
    }
    
    public static function getColumns(): array
    {
        return [
            'id' => ['property' => 'id', 'type' => 'int'],
            'nama' => ['property' => 'nama', 'type' => 'string'],
            'alamat' => ['property' => 'alamat', 'type' => 'text'],
            'no_hp' => ['property' => 'no_hp', 'type' => 'text'],
            'status' => ['property' => 'status', 'type' => 'string'],
            'created_at' => ['property' => 'createdAt', 'type' => 'datetime'],
            'paket_id' => ['property' => 'paket_id', 'type' => 'int'],
            'pop' => ['property' => 'pop', 'type' => 'text'],
        ];
    }

}
