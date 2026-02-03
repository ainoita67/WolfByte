<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\UsuarioService;

class UsuarioController
{
    private UsuarioService $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    // Listar usuarios activos
    public function index(Request $req, Response $res): void
    {
        try {
            $usuarios = $this->service->getAllUsuarios();
            $res->status(200)->json($usuarios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Listar usuarios inactivos
    public function indexin(Request $req, Response $res): void
    {
        try {
            $usuarios = $this->service->getInactiveUsuarios();
            $res->status(200)->json($usuarios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Mostrar usuario por ID
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $usuario = $this->service->getUsuarioById((int)$id);
            $res->status(200)->json($usuario);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Mostrar nombre por ID
    public function showName(Request $req, Response $res, string $id): void
    {
        try {
            $nombre = $this->service->getNombreById((int)$id);
            $res->status(200)->json(['nombre' => $nombre]);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Mostrar correo por ID
    public function showEmail(Request $req, Response $res, string $id): void
    {
        try {
            $correo = $this->service->getEmailById((int)$id);
            $res->status(200)->json(['correo' => $correo]);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Mostrar rol por ID
    public function showRol(Request $req, Response $res, string $id): void
    {
        try {
            $rol = $this->service->getRolById((int)$id);
            $res->status(200)->json($rol);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Mostrar token por ID
    public function showToken(Request $req, Response $res, string $id): void
    {
        try {
            $tokenData = $this->service->getTokenById((int)$id);
            $res->status(200)->json(['token' => $tokenData]);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Crear usuario
    public function store(Request $req, Response $res): void
    {
        try {
            $result = $this->service->createUsuario($req->json());
            $res->status(201)->json(['id' => $result['id']], "Usuario creado correctamente");
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Actualizar usuario
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $result = $this->service->updateUsuario((int)$id, $req->json());
            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Actualizar contraseña
    public function updatePassword(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json();
            if (!isset($data['password'])) {
                $res->status(422)->json(['errors' => ['password' => 'Campo requerido']]);
                return;
            }

            $result = $this->service->updatePassword((int)$id, $data['password']);
            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Activar / desactivar usuario
    public function inactive(Request $req, Response $res, string $id): void
    {
        try {
            $result = $this->service->toggleActiveStatus((int)$id);
            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
