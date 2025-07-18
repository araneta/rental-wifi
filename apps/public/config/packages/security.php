<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    // Password Hasher (not used unless you decide to hash/check with Symfony later)
    $security->passwordHasher('Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface')
        ->algorithm('auto');

    // Dummy user provider (not used, but required for structure)
    $provider = $security->provider('users_in_memory')->memory();
    $provider->user('test@example.com')
        ->password('$2y$13$z4xV5hdz2N/CMKfPUNFjqOv1l.yWnP6umrhiVZf2lMg6XolbI6pl6')
        ->roles(['ROLE_USER']);

    // Dev firewall (debug toolbar)
    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    // Auth login firewall - allow anonymous access to /auth/login (handled manually)
    $security->firewall('login')
        ->pattern('^/auth/login')
        ->stateless(true)
        ->security(false); // ✅ No authentication enforced

    // API firewall - protects all /api/* routes using your custom JWT authenticator
    $security->firewall('api')
        ->pattern('^/api')
        ->stateless(true)
        ->customAuthenticators([
            'App\Security\JWTAuthenticator', // ✅ your class that reads and validates JWT token
        ]);

    // Default/main firewall
    $security->firewall('main')
        ->lazy(true)
        ->provider('users_in_memory');

    // Access rules
    $security->accessControl()
        ->path('^/auth/login')
        ->roles(['IS_AUTHENTICATED_ANONYMOUSLY']);

    $security->accessControl()
        ->path('^/api')
        ->roles(['IS_AUTHENTICATED_FULLY']);
};
