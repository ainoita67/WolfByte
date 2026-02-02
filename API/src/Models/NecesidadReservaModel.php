<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class NecesidadReservaModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todas las necesidades de reserva
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Necesidad_R_espacio")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener necesidades de reserva");
        }
    }

    /**
     * Obtener necesidad de reserva por ID
     */
    public function findById(int $id): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Necesidad_R_espacio WHERE id_reserva_espacio = :id")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la necesidad de reserva");
        }
    }

    /**
     * Crear necesidad de reserva
     */
    public function create(array $data): array
    {
        try {
            $this->db
                ->query("
                    INSERT INTO Necesidad_R_espacio (id_reserva_espacio, id_necesidad)
                    VALUES (:reserva, :necesidad)
                ")
                ->bind(':reserva', $data['id_reserva_espacio'])
                ->bind(':necesidad', $data['id_necesidad'])
                ->execute();

            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la necesidad de reserva");
        }
    }

    /**
     * Actualizar necesidad de reserva
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Necesidad_R_espacio
                    SET id_necesidad = :necesidad
                    WHERE id_reserva_espacio = :id
                ")
                ->bind(':necesidad', $data['id_necesidad'])
                ->bind(':id', $id)
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la necesidad de reserva");
        }
    }

    /**
     * Eliminar necesidad de reserva
     */
    public function delete(int $id): void
    {
        try {
            $this->db
                ->query("DELETE FROM Necesidad_R_espacio WHERE id_reserva_espacio = :id")
                ->bind(':id', $id)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar la necesidad de reserva");
        }
    }
}