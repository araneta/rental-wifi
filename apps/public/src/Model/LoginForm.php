<?php
namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;

class LoginForm
{
    public function __construct(
        #[Assert\NotBlank]
        public string $email,

        #[Assert\NotBlank]
        public string $password,

        
    ) 
    {
    }
}
