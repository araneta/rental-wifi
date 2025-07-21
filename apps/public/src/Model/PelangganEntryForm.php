<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of PelangganEntryForm
 *
 * @author aldo
 */
class PelangganEntryForm {
    //put your code here
    public function __construct(
            #[Assert\NotBlank]
            public string $nama,
            
            #[Assert\NotBlank]
            public string $alamat,
            
            #[Assert\NotBlank]
            public string $no_hp,
            
            #[Assert\Positive]
            public int $paket_id,
            
            #[Assert\NotBlank]
            public string $pop,
            
            #[Assert\NotBlank]
            public string $status,
    ) {
        
    }
}
