<?php
declare(strict_types=1);

// Services/UsuarioService.php

namespace Services;

use Models\UsuarioModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class UsuarioService
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

// $router->get('/user',               'Controllers\\UsuarioController@index'); // Se reciben los datos de los usuarios para listarlos

    public function getAllUsuarios(): array
    {
        try {
            return $this->model->findAll();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

// $router->get('/user/{id}',          'Controllers\\UsuarioController@show'); // Se reciben los datos del usuario con el id que se mande

    public function getUsuarioById(int $id): array
    {   
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);
        try {
            $usuario = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$usuario) {
            throw new \Exception("Usuario no encontrado", 404);
        }

        return $usuario;
    }

    /**
     * Crear nuevo usuario
     */
    public function createUsuario(array $input): array
    {
        $data = Validator::validate($input, [
            'nombre'        => 'required|string|min:3|max:100',
            'correo'        => 'required|email|max:150',
            'contrasena'    => 'required|string|min:6|max:255',
            'id_rol'        => 'required|int|min:1'
        ]);

        try {
            $existe = $this->model->emailExists($data['correo']);
            if ($existe) {
                throw new \Exception("Ya existe un usuario con ese correo", 422);
            }
            $id = $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$id) {
            throw new \Exception("No se pudo crear el usuario");
        }

        return ['id' => $id];
    }

// $router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el id

    public function updateUsuario(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'nombre'        => 'required|string|min:3|max:100',
            'correo'        => 'required|email|max:150',
            'contrasena'    => 'required|string|min:6|max:255',
            'id_rol'        => 'required|int|min:1',
            'usuario_activo'=> 'required|bool'
        ]);

        try {
            $existe = $this->model->emailExistsForOtherUser($data['correo'], $id);
            if ($existe) {
                throw new \Exception("Ya existe un usuario con ese correo", 422);
            }
            $result = $this->model->update($id, $data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if ($result === 0) {
            $exists = $this->model->findById($id);

            if (!$exists) {
                throw new \Exception("Usuario no encontrado", 404);
            }

            return [
                'status' => 'no_changes',
                'message' => 'No hubo cambios en los datos del usuario'
            ];
        }

        return [
            'status' => 'updated',
            'message' => 'Usuario actualizado correctamente'
        ];
    }

// $router->patch('/user/{id}/active',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id

    public function toggleActiveStatus(int $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            $isActive = $this->model->isActive($id);
            if (!$isActive) {
                $result = $this->model->setActive($id);
            } else {
                $result = $this->model->setInactive($id);
            }
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if ($result === 0) {
            $exists = $this->model->findById($id);

            if (!$exists) {
                throw new \Exception("Usuario no encontrado", 404);
            }

            return [
                'status' => 'no_changes',
                'message' => 'No hubo cambios en el estado del usuario'
            ];
        }

        return [
            'status' => 'updated',
            'message' => 'Estado del usuario actualizado correctamente'
        ];
    }

// $router->patch('/user/{id}/token',       'Controllers\\UsuarioController@setToken'); // Se guarda un token y su fecha de expiración del usuario del que se pase el id

    public function setToken(int $id, string $token, string $expiration): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            $result = $this->model->setToken($id, $token, $expiration);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if ($result === 0) {
            $exists = $this->model->findById($id);

            if (!$exists) {
                throw new \Exception("Usuario no encontrado", 404);
            }

            return [
                'status' => 'no_changes',
                'message' => 'No hubo cambios en el token del usuario'
            ];
        }

        return [
            'status' => 'updated',
            'message' => 'Token del usuario actualizado correctamente'
        ];
    }
    
}
