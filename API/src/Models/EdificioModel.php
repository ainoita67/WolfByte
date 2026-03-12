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
                ->query("SELECT * FROM Edificio ORDER BY nombre_edificio")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener edificios: " . $e->getMessage());
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
            throw new \Exception("Error al buscar edificio: " . $e->getMessage());
        }
    }

    /**
     * Crear edificio
     */
    public function create(array $data): array
    {
        try {
            $this->db
                ->query("INSERT INTO Edificio (nombre_edificio) VALUES (:nombre)")
                ->bind(':nombre', $data['nombre_edificio'])
                ->execute();

            $id = (int)$this->db->lastId();
            
            return $this->findById($id);
            
        } catch (PDOException $e) {
            throw new \Exception("Error al crear edificio: " . $e->getMessage());
        }
    }

    /**
     * Actualizar edificio
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db
                ->query("UPDATE Edificio SET nombre_edificio = :nombre WHERE id_edificio = :id")
                ->bind(':nombre', $data['nombre_edificio'])
                ->bind(':id', $id)
                ->execute();

            return $this->findById($id);
            
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar edificio: " . $e->getMessage());
        }
    }

    /**
     * Eliminar edificio
     */
    public function delete(int $id): void
    {
        try {
            $this->db
                ->query("DELETE FROM Edificio WHERE id_edificio = :id")
                ->bind(':id', $id)
                ->execute();
                
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar edificio: " . $e->getMessage());
        }
    }
}