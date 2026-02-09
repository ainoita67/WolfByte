<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

//die('CARGADO UsuarioModel CORRECTO');

class UsuarioModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function findByEmail(string $email): array|false
    {
        return $this->db
            ->query("SELECT * FROM Usuario WHERE correo = :email")
            ->bind(':email', $email)
            ->fetch();
    }

    // Otros métodos CRUD

// $router->get('/user',               'Controllers\\UsuarioController@index'); // Se reciben los datos de los usuarios para listarlos

    public function findAll(): array
    {
        return $this->db
            ->query("SELECT * FROM Usuario")
            ->fetchAll();
    }

// $router->get('/user/{id}',          'Controllers\\UsuarioController@show'); // Se reciben los datos del usuario con el id que se mande

    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT * FROM Usuario WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->fetch();
    }

    /**
     * existe un usuario con el correo dado
     */
    public function emailExists(string $email): bool
        {
            $result = $this->db
                ->query("SELECT 1 FROM Usuario WHERE correo = :correo LIMIT 1")
                ->bind(':correo', $email)
                ->fetch();

            return $result !== false;
        }

    /**
     * Comprobar si existe otro usuario con el mismo correo (excluyendo un ID)
     */
    public function emailExistsForOtherUser(string $email, int $excludeId): bool
    {
        $result = $this->db
            ->query(
                "SELECT 1 
                FROM Usuario 
                WHERE correo = :correo 
                AND id_usuario != :id
                LIMIT 1"
            )
            ->bind(':correo', $email)
            ->bind(':id', $excludeId)
            ->fetch();

        return $result !== false;
    }

    /**
     * Crear nuevo usuario
     */
    public function create(array $data): int
    {
        $this->db
            ->query("INSERT INTO Usuario (nombre, correo, contrasena, id_rol) VALUES (:nombre, :correo, :contrasena, :id_rol)")
            ->bind(':nombre', $data['nombre'])
            ->bind(':correo', $data['correo'])
            ->bind(':contrasena', $data['contrasena'])
            ->bind(':id_rol', $data['id_rol'])
            ->execute();
        return (int) $this->db->lastId();
    }

// $router->put('/user/{id}',          'Controllers\\UsuarioController@update'); // Se modifica por completo todos los campos del usuario del que se pase el id

    public function update(int $id, array $data): bool
    {
        $this->db
            ->query("UPDATE Usuario SET nombre = :nombre, correo = :correo, contrasena = :contrasena, id_rol = :id_rol, usuario_activo = :usuario_activo WHERE id_usuario = :id")
            ->bind(':nombre', $data['nombre'])
            ->bind(':correo', $data['correo'])
            ->bind(':contrasena', $data['contrasena'])
            ->bind(':id_rol', $data['id_rol'])
            ->bind(':usuario_activo', $data['usuario_activo'])
            ->bind(':id', $id)
            ->execute();
        return $this->db->rowCount() > 0;
    }

// $router->patch('/user/{id}/active',       'Controllers\\UsuarioController@inactive'); // Se modifica el campo de active a incactive o de inactive a active del usuario del que se pase el id

    public function setInactive(int $id): bool
    {
        $this->db
            ->query("UPDATE Usuario SET usuario_activo = false WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->execute();
        return $this->db->rowCount() > 0;
    }

    public function setActive(int $id): bool
    {
        $this->db
            ->query("UPDATE Usuario SET usuario_activo = true WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->execute();
        return $this->db->rowCount() > 0;
    }

    public function isActive(int $id): bool
    {
        $result = $this->db
            ->query("SELECT usuario_activo FROM Usuario WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ? (bool)$result['usuario_activo'] : false;
    }

// $router->patch('/user/{id}/token',       'Controllers\\UsuarioController@setToken'); // Se guarda un token y su fecha de expiración del usuario del que se pase el id

    public function setToken(int $id, string $token, string $expiration): bool
    {
        $this->db
            ->query("UPDATE Usuario SET token = :token, expira_token = :expiration WHERE id_usuario = :id")
            ->bind(':token', $token)    
            ->bind(':expiration', $expiration)
            ->bind(':id', $id)
            ->execute();
        return $this->db->rowCount() > 0;
    }

}