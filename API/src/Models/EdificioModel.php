<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class EdificioModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todos los edificios
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Edificio")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener edificios");
        }
    }

    /**
     * Obtener edificio por ID
     */
    public function findById(int $id): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Edificio WHERE id_edificio = :id")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar el edificio");
        }
    }

    /**
     * Crear edificio
     */
    public function create(array $data): array
    {
        try {
            $this->db
                ->query("
                    INSERT INTO Edificio (nombre_edificio)
                    VALUES (:nombre)
                ")
                ->bind(':nombre', $data['nombre_edificio'])
                ->execute();

            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear el edificio");
        }
    }

    /**
     * Actualizar edificio
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Edificio
                    SET nombre_edificio = :nombre
                    WHERE id_edificio = :id
                ")
                ->bind(':nombre', $data['nombre_edificio'])
                ->bind(':id', $id)
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar el edificio");
        }
    }

    /**
     * Eliminar edificio
     */
    public function deleteById(int $id): void
    {
        try {
            $this->db
                ->query("DELETE FROM Edificio WHERE id_edificio = :id")
                ->bind(':id', $id)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar el edificio");
        }
    }
}
