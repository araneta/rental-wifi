<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Description of TagihanUpdateForm
 *
 * @author aldo
 */
class TagihanUpdateForm {
    //put your code here
    public function __construct(
            #[Assert\NotBlank]
            public string $tanggal_bayar,
            
            #[Assert\NotBlank]
            public string $status,
            
            #[Assert\NotBlank]
            public string $metode_pembayaran,
            
    ) {
        
    }
}
