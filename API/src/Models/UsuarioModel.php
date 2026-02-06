<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

class UsuarioModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Buscar usuario por correo
     */
    public function findByEmail(string $email): array|false
    {
        return $this->db
            ->query("SELECT id_usuario, nombre, correo, contrasena AS password, id_rol, usuario_activo FROM Usuario WHERE correo = :email")
            ->bind(':email', $email)
            ->fetch();
    }

    /**
     * Obtener todos los usuarios activos
     */
    public function findActive(): array
    {
        return $this->db
            ->query("SELECT id_usuario, nombre, correo, id_rol, usuario_activo FROM Usuario WHERE usuario_activo = 1")
            ->fetchAll();
    }

    /**
     * Obtener todos los usuarios inactivos
     */
    public function findInactive(): array
    {
        return $this->db
            ->query("SELECT id_usuario, nombre, correo, id_rol, usuario_activo FROM Usuario WHERE usuario_activo = 0")
            ->fetchAll();
    }

    /**
     * Buscar usuario por ID
     */
    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT id_usuario, nombre, correo, id_rol, usuario_activo FROM Usuario WHERE id_usuario = :id")
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
            ->query("INSERT INTO Usuario (nombre, correo, contrasena, id_rol) 
                     VALUES (:nombre, :correo, :contrasena, :id_rol)")
            ->bind(':nombre', $data['nombre'])
            ->bind(':correo', $data['correo'])
            ->bind(':contrasena', $data['contrasena'])
            ->bind(':id_rol', $data['id_rol'])
            ->execute();

        return (int) $this->db->lastId();
    }

    /**
     * Actualizar usuario (sin contraseña)
     */
    public function update(int $id, array $data): bool
    {
        $this->db
            ->query("UPDATE Usuario 
                     SET nombre = :nombre, correo = :correo, id_rol = :id_rol, usuario_activo = :usuario_activo 
                     WHERE id_usuario = :id")
            ->bind(':nombre', $data['nombre'])
            ->bind(':correo', $data['correo'])
            ->bind(':id_rol', $data['id_rol'])
            ->bind(':usuario_activo', $data['usuario_activo'])
            ->bind(':id', $id)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(int $id, string $password): bool
    {
        $this->db
            ->query("UPDATE Usuario SET contrasena = :contrasena WHERE id_usuario = :id")
            ->bind(':contrasena', $password)
            ->bind(':id', $id)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Activar usuario
     */
    public function setActive(int $id): bool
    {
        $this->db
            ->query("UPDATE Usuario SET usuario_activo = true WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Desactivar usuario
     */
    public function setInactive(int $id): bool
    {
        $this->db
            ->query("UPDATE Usuario SET usuario_activo = false WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Verificar si usuario está activo
     */
    public function isActive(int $id): bool
    {
        $result = $this->db
            ->query("SELECT usuario_activo FROM Usuario WHERE id_usuario = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ? (bool) $result['usuario_activo'] : false;
    }
}
