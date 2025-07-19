<?php

namespace App\Model;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of UserEntryForm
 *
 * @author aldo
 */
class UserEntryForm {

    //put your code here
    public function __construct(
            #[Assert\NotBlank]
            public string $name,
            
            #[Assert\NotBlank]
            public string $email,
            
            #[Assert\NotBlank]
            public string $password,
            
            #[Assert\NotBlank]
            public string $role,
    ) {
        
    }
}
