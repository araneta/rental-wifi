<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Description of PaketEntryForm
 *
 * @author aldo
 */
class PaketEntryForm {
    //put your code here
    //put your code here
    public function __construct(
            #[Assert\NotBlank]
            public string $nama,
            
            #[Assert\NotBlank]
            public string $kecepatan,
            
            #[Assert\Positive]
            public int $harga,
            
            #[Assert\NotBlank]
            public string $deskripsi,
    ) {
        
    }
}
