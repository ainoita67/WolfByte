<?php
declare(strict_types=1);

namespace Controllers;

use Models\UsuarioModel;
use Firebase\JWT\JWT;
use Core\Request;
use Core\Response;

class AuthController
{
    public function login(Request $request, Response $response): void
    {
        $input = $request->getBody();

        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        if (empty($email) || empty($password)) {
            $response->status(400)->json([], 'Email y password obligatorios');
            return;
        }

        $userModel = new UsuarioModel();
        $user = $userModel->findByEmail($email);

        if (!$user || $password !== $user['password']) {
            $response->status(401)->json([], 'Credenciales incorrectas');
            return;
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + JWT_EXPIRE,
            'sub' => $user['id_usuario'],
            'rol' => $user['id_rol'],
            'nombre' => $user['nombre'],
            'email' => $user['correo']
        ];

        $token = JWT::encode($payload, JWT_SECRET, 'HS256');

        $response->json(['token' => $token], 'Login correcto');
    }
}
    