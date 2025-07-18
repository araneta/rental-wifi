<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
final class UserController extends AbstractController
{
    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    public function me(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Token not found'], 401);
        }

        $user = $token->getUser();
        $claims = $token->getPayload();

        return $this->json([
            'user_id' => $claims['id'] ?? null,
            'email' => $claims['email'] ?? null
        ]);
    }
}
