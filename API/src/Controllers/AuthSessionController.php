<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
use Services\AuthSessionService;
use Throwable;
use Validation\ValidationException;

class AuthSessionController
{
    private AuthSessionService $service;

    public function __construct()
    {
        $this->service = new AuthSessionService();
        // Aseguramos que la sesión de PHP esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(Request $req, Response $res): void
    {
        try {
            $data = $req->json(); // {email, password}
            $user = $this->service->login(
                $data['email'] ?? '',
                $data['password'] ?? ''
            );

            // Guardamos sesión tradicional PHP
            $_SESSION['user'] = $user;

            // También usamos tu Session helper si lo quieres mantener
            Session::createUserSession($user);

            $res->status(200)->json([
                'user' => $user,
                'session_id' => session_id() // Opcional, para debugging
            ], "Usuario logueado correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
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

    /**
     * Opcional: para verificar si hay un usuario logueado
     */
    public function check(Request $req, Response $res): void
    {
        $user = $_SESSION['user'] ?? null;
        if ($user) {
            $res->status(200)->json(['user' => $user], "Usuario activo");
        } else {
            $res->status(401)->json([], "No hay usuario logueado");
        }
    }
}
