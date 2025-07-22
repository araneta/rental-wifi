<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Description of TagihanEntryForm
 *
 * @author aldo
 */
class TagihanEntryForm {
    //put your code here
    
    public function __construct(
            #[Assert\NotBlank]
            public string $tanggal_bayar,
            
            #[Assert\NotBlank]
            public string $status,
            
            #[Assert\Positive]
            public int $jumlah,
            
            #[Assert\Positive]
            public int $pelanggan_id,
            
            #[Assert\NotBlank]
            public string $bulan_tahun,
            
            #[Assert\NotBlank]
            public string $metode_pembayaran,
            
            #[Assert\Positive]
            public int $petugas_id,
    ) {
        
    }
}
