<?php
declare(strict_types=1);

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

    /**
     * Obtener todos los usuarios activos
     */
    public function getAllUsuarios(): array
    {
        try {
            return $this->model->findActive();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener todos los usuarios inactivos
     */
    public function getInactiveUsuarios(): array
    {
        try {
            return $this->model->findInactive();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener usuario por ID
     */
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

    /**
     * Actualizar usuario por ID (sin tocar la contraseña)
     */
    public function updateUsuario(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'nombre'         => 'required|string|min:3|max:100',
            'correo'         => 'required|email|max:150',
            'id_rol'         => 'required|int|min:1',
            'usuario_activo' => 'required|bool'
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

        if (!$result) {
            $this->ensureUserExists($id);
            return [
                'status'  => 'no_changes',
                'message' => 'No hubo cambios en los datos del usuario'
            ];
        }

        return [
            'status'  => 'updated',
            'message' => 'Usuario actualizado correctamente'
        ];
    }

    /**
     * Cambiar estado activo/inactivo de un usuario
     */
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

        if (!$result) {
            $this->ensureUserExists($id);
            return [
                'status'  => 'no_changes',
                'message' => 'No hubo cambios en el estado del usuario'
            ];
        }

        return [
            'status'  => 'updated',
            'message' => 'Estado del usuario actualizado correctamente'
        ];
    }

    /**
     * Actualizar contraseña de un usuario
     */
    public function updatePassword(int $id, string $password): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        Validator::validate(['password' => $password], [
            'password' => 'required|string|min:6|max:255'
        ]);

        try {
            $result = $this->model->updatePassword($id, $password);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$result) {
            $this->ensureUserExists($id);
            return [
                'status'  => 'no_changes',
                'message' => 'No hubo cambios en la contraseña del usuario'
            ];
        }

        return [
            'status'  => 'updated',
            'message' => 'Contraseña del usuario actualizada correctamente'
        ];
    }

    /**
     * Verificar si el usuario existe
     */
    private function ensureUserExists(int $id): void
    {
        if (!$this->model->findById($id)) {
            throw new \Exception("Usuario no encontrado", 404);
        }
    }
}
