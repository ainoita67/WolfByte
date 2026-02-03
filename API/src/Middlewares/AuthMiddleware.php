<?php
declare(strict_types=1);

namespace Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Core\Request;

class AuthMiddleware
{
    public static function verify(Request $request): ?array
    {
        $authHeader = $request->getHeader('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = trim(str_replace('Bearer ', '', $authHeader));

        try {
            $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
