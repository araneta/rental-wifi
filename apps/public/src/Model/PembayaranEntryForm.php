<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Description of PembayaranEntryForm
 *
 * @author aldo
 */
class PembayaranEntryForm {
    //put your code here
    
    public function __construct(
           #[Assert\Positive]
            public int $jumlah,
            
            #[Assert\NotBlank]
            public string $metode_pembayaran,
            
            #[Assert\Positive]
            public int $pelanggan_id,
            
            //#[Assert\Positive]
            //public int $petugas_id,
            
            
            #[Assert\NotBlank]
            public string $tanggal_pembayaran,
            
            #[Assert\Positive]
            public int $tagihan_id,
    ) {
        
    }
}
