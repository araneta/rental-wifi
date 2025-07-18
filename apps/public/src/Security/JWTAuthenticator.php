<?php
// src/Security/JWTAuthenticator.php

namespace App\Security;

use App\Service\JWTService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; // ✅ Correct namespace!

class JWTAuthenticator extends AbstractAuthenticator
{
    private JWTService $jwtService;
    private UserProviderInterface $userProvider;

    public function __construct(JWTService $jwtService, UserProviderInterface $userProvider)
    {
        $this->jwtService = $jwtService;
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->getPathInfo(), '/api');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('Missing token');
        }

        $token = substr($authHeader, 7);
        $data = $this->jwtService->decodeToken($token);

        if (!$data || empty($data['username'])) {
            throw new AuthenticationException('Invalid token');
        }

        return new SelfValidatingPassport(
            new UserBadge($data['username'], function ($username): UserInterface {
                return $this->userProvider->loadUserByIdentifier($username);
            })
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response('Authentication Failed: ' . $exception->getMessage(), 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Let the request continue
    }
}
