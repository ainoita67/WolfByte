<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\UsuarioService;
use Services\LogAccionesService;

class UsuarioController
{
    private UsuarioService $service;
    private LogAccionesService $serviceLog;

    public function __construct()
    {
        $this->service = new UsuarioService();
        $this->serviceLog = new LogAccionesService();
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

    // Crear usuario
    public function store(Request $req, Response $res): void
    {
        try {
            $data=$req->json();
            $log['id_usuario_actor']=$data['id_usuario_actor'];
            $usuario = $this->service->createUsuario($data);
            $log['id_usuario']=$usuario['id'];
            $this->serviceLog->createLog('Creación de usuario', $log);
            $res->status(201)->json(['id' => $usuario['id']], "Usuario creado correctamente");
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
            $data=$req->json();
            $log['id_usuario_actor']=$data['id_usuario_actor'];
            $usuario = $this->service->updateUsuario((int)$id, $data);
            $log['id_usuario']=$id;
            if($usuario['status']!='no_changes'){
                $this->serviceLog->createLog('Modificación de usuario', $log);
            }
            $res->status(200)->json([], $usuario['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function patch(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json(); // Puede ser null o []
            $log['id_usuario_actor']=$data['id_usuario_actor'];

            // Si se envía contraseña → actualizar
            if (isset($data['password']) && trim($data['password']) !== '') {
                $usuario = $this->service->updatePassword((int)$id, $data['password']);
                $log['id_usuario']=$id;
                $this->serviceLog->createLog('Modificación de usuario', $log);
                $res->status(200)->json([], $usuario['message']);
                return;
            }

            // Si no se envía contraseña → activar / desactivar
            $usuario = $this->service->toggleActiveStatus((int)$id);
            $log['id_usuario']=$id;
            if($this->service->getUsuarioById((int)$id)['usuario_activo']){
                $this->serviceLog->createLog('Activación de usuario', $log);
            }else{
                $this->serviceLog->createLog('Desactivación de usuario', $log);
            }
            $res->status(200)->json([], $usuario['message']);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
