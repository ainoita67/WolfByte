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
            ->query("SELECT *, contrasena AS password FROM Usuario WHERE correo = :email")
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

// $router->get('/user/{id}/nombre',   'Controllers\\UsuarioController@showName'); // Se recibe el nombre del usuario del que se pase el id

    public function findNameById(int $id): string|false
    {
        $result = $this->db
            ->query("SELECT nombre FROM Usuario WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ? $result['nombre'] : false;
    }

// $router->get('/user/{id}/correo',   'Controllers\\UsuarioController@showEmail'); // Se recibe el correo del usuario del que se pase el id

    public function findEmailById(int $id): string|false
    {
        $result = $this->db
            ->query("SELECT correo FROM Usuario WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ? $result['correo'] : false;
    }

// $router->get('/user/{id}/rol',      'Controllers\\UsuarioController@showRol'); // Se recibe el rol del usuario del que se pase el id

    public function findRolById(int $id): string|false
    {
        $result = $this->db
            ->query("SELECT
                    r.rol,
                    r.id_rol
                FROM
                    Usuario u
                JOIN Rol r ON u.`id_rol` = r.id_rol
                WHERE
                    id_usuario = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ? $result['rol'] : false;
    }

// $router->get('/user/{$id}/token',   'Controllers\\UsuarioController@showToken'); // Se recibe el token  y su fecha de expiración del usuario del que se pase el id

    public function findTokenById(int $id): array|false
    {
        $result = $this->db
            ->query("SELECT token, expira_token FROM Usuario WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ? $result : false;
    }

// $router->post('/user',              'Controllers\\UsuarioController@store'); // Se envían los datos del usuario desde un formulario para añadirlo a la DDBB

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