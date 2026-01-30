<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class CaracteristicaModel
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
        return $this->db
            ->query("SELECT * FROM Caracteristica")
            ->fetchAll();
    }
    /**
     * Obtener edificio por ID
     */
    public function findById(int $id): ?array
    {
        $result = $this->db
            ->query("SELECT * FROM Caracteristica WHERE id_caracteristica = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ?: null;
    }

    /**
     * Crear edificio
     */
    public function create(array $data): int|false
    {
        $this->db
            ->query("
                    INSERT INTO Caracteristica (nombre)
                    VALUES (:nombre)
                ")
            ->bind(':nombre', $data['nombre'])
            ->execute();

        return (int) $this->db->lastId();
    }

    /**
     * Actualizar edificio
     */
    public function update(int $id, array $data): array
    {
        $this->db
            ->query("
                    UPDATE Caracteristica
                    SET nombre = :nombre
                    WHERE id_caracteristica = :id
                ")
            ->bind(':nombre', $data['nombre'])
            ->bind(':id', $id)
            ->execute();

        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'];
    }

    /**
     * Eliminar edificio
     */
    public function delete(int $id): void
    {
        $this->db
            ->query("DELETE FROM Caracteristica WHERE id_caracteristica = :id")
            ->bind(':id', $id)
            ->execute();
            
        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'];
    }
}