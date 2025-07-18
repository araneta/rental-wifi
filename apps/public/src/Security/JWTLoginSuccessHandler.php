<?php
// src/Security/JWTLoginSuccessHandler.php
namespace App\Security;

use App\Service\JWTService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; // ✅ correct!

use Symfony\Component\HttpFoundation\Request;

class JWTLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $jwt = $this->jwtService->createToken($token->getUser());
        return new JsonResponse(['token' => $jwt]);
    }
}
