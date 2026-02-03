<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaEspacioModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Crear registro de reserva de espacio
     */
    public function create(array $data): array
    {
        try {
            $this->db->query("
                INSERT INTO Reserva_espacio (
                    id_reserva_espacio,
                    actividad,
                    id_espacio
                ) VALUES (
                    :id_reserva_espacio,
                    :actividad,
                    :id_espacio
                )
            ")
            
            ->bind(':id_reserva_espacio', $id['id_reserva_espacio'])
            ->bind(':actividad', $data['actividad'])
            ->bind(':id_espacio', $data['id_espacio'])
            ->execute();

            return $this->findById((int)$data['id_reserva_espacio']);
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la reserva de espacio: " . $e->getMessage());
        }
    }

    /**
     * Obtener reserva de espacio por ID de reserva
     */
    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT * FROM Reserva_espacio WHERE id_reserva_espacio = :id")
            ->bind(':id', $id)
            ->fetch();
    }

    /**
     * Obtener todas las reservas de espacios
     */
    public function getAll(): array
    {
        return $this->db
            ->query("SELECT * FROM Reserva_espacio ORDER BY id_reserva_espacio DESC")
            ->fetchAll();
    }

    /**
     * Obtener reservas por ID de espacio
     */
    public function getByEspacio($idEspacio): array
    {
        return $this->db
            ->query("SELECT * FROM Reserva_espacio WHERE id_espacio = :id_espacio ORDER BY id_reserva_espacio DESC")
            ->bind(':id_espacio', $idEspacio)
            ->fetchAll();
    }
    /**
     * Actualizar reserva de espacio
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db->query("
                UPDATE Reserva_espacio SET
                    actividad = :actividad,
                    id_espacio = :id_espacio
                WHERE id_reserva_espacio = :id_reserva_espacio
            ")
            ->bind(':actividad', $data['actividad'])
            ->bind(':id_espacio', $data['id_espacio'])
            ->bind(':id_reserva_espacio', $id)
            ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la reserva de espacio: " . $e->getMessage());
        }
    }

    /**
     * Eliminar reserva de espacio
     */
    public function delete(int $id): void
    {
        try {
            $this->db->query("DELETE FROM Reserva_espacio WHERE id_reserva_espacio = :id")
                ->bind(':id', $id)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar la reserva de espacio: " . $e->getMessage());
        }
    }
}