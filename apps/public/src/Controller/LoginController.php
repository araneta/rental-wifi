<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

use PDO;
use DrizzlePHP\DrizzlePHP;
use App\Schemas\UsersSchema;
use App\Model\LoginForm;
use App\Service\MessageGenerator;


final class LoginController extends AbstractController
{
	 
	public function __construct(
        private MessageGenerator $messageGenerator,
    ) {
    }
    #[Route('/api/login', name: 'app_login')]
    public function index(#[MapRequestPayload] LoginForm $form): JsonResponse
    {
		$message = $this->messageGenerator->getHappyMessage();
		
		// Database connection
		$pdo = new \PDO('mysql:host=172.17.0.1;dbname=rentalwifi', 'rentalwifi', 'willamette');
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$db = new DrizzlePHP($pdo);

		// Basic select
		$users = $db->select(UsersSchema::class)
			->where('email', '=', $form->username)
			->where('password', '=', $form->password)
			->orderBy('name', 'ASC')
			->limit(10)
			->get();
		
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LoginController.php',
            'users'=> $users,
            'form'=>$form,
            'message'=>$message
        ]);
    }
}
