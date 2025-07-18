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
use App\Service\DrizzleService;

final class LoginController extends AbstractController
{
	 
	public function __construct(
        private MessageGenerator $messageGenerator,
        private DrizzleService $drizzleService
    ) {}
    
    
    #[Route('/api/login', name: 'app_login')]
    public function index(#[MapRequestPayload] LoginForm $form): JsonResponse
    {
		$message = $this->messageGenerator->getHappyMessage();
		
		$db = $this->drizzleService->getDb();

        $users = $db->select(UsersSchema::class)
            ->where('email', '=', $form->username)
            ->where('password', '=', $form->password)
            ->orderBy('name', 'ASC')
            ->limit(10)
            ->get();

        return $this->json([
            'message' => $this->messageGenerator->getHappyMessage(),
            'users' => $users,
            'form' => $form,
        ]);
    }
}
