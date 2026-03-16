<?php
declare(strict_types=1);

namespace Controllers;

use Models\UsuarioModel;
use Firebase\JWT\JWT;
use Core\Request;
use Core\Response;
use Core\Session;

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

        if (!$user || !$user['usuario_activo']) {
            $response->status(401)->json([], 'Credenciales incorrectas');
            return;
        }

        if (!password_verify($password, $user['password'])) {
            $response->status(401)->json([], 'Credenciales incorrectas');
            return;
        }

        $payload = [
            'iat' => time(),
            'exp' => time() + JWT_EXPIRE,
            'id_usuario' => $user['id_usuario'],
            'rol' => $user['id_rol'],
            'nombre' => $user['nombre'],
            'email' => $user['correo']
        ];

        $token = JWT::encode($payload, JWT_SECRET, 'HS256');

        $response->json(['token' => $token], 'Login correcto');
    }

    public function logout(Request $req, Response $res): void
    {
        // Destruye la sesión PHP
        if (session_status() !== PHP_SESSION_NONE) {
            $_SESSION = [];
            session_destroy();
        }

        // También destruye la sesión en tu helper
        Session::destroy();

        $res->status(200)->json([], "Sesión cerrada correctamente");
    }
}