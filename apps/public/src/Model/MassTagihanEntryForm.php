<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Description of MassTagihanEntryForm
 *
 * @author aldo
 */
class MassTagihanEntryForm {
    //put your code here
    
    public function __construct(
            
            #[Assert\NotBlank]
            public string $bulan_tahun,
                        
            #[Assert\Positive]
            public int $petugas_id,
    ) {
        
    }
}
