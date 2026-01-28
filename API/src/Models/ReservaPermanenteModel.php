<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaPermanenteModel
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
                ->query("SELECT * FROM Reserva_permanente WHERE activo=1")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas permanentes");
        }
    }

    /**
     * Obtener reserva permanente por ID
     */
    public function findById(int $id): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva_permanente WHERE id_reserva_permanente = :id")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la reserva permanente");
        }
    }

    /**
     * Obtener reserva permanente por ID de recurso
     */
    public function findByIdRecurso(string $id_recurso): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva_permanente WHERE id_recurso = :id_recurso AND activo=1")
                ->bind(':id_recurso', $id_recurso)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la reserva permanente");
        }
    }

    /**
     * Crear reserva permanente
     */
    public function create(array $data): array
    {
        try {
            $this->db
                ->query("
                    INSERT INTO Reserva_permanente (inicio, fin, comentario, activo, id_recurso)
                    VALUES (:inicio, :fin, :comentario, :activo, :id_recurso)
                ")
                ->bind(':inicio',       $data['inicio'])
                ->bind(':fin',          $data['fin'])
                ->bind(':comentario',   $data['comentario'] ?? null)
                ->bind(':activo',       $data['activo'])
                ->bind(':id_recurso',   $data['id_recurso'])
                ->execute();

            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la reserva permanente");
        }
    }

    /**
     * Actualizar reserva permanente
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva_permanente SET
                        inicio = :inicio,
                        fin = :fin,
                        comentario = :comentario,
                        activo = :activo,
                        id_recurso = :id_recurso
                    WHERE id_reserva_permanente = :id
                ")
                ->bind(':id',           $id)
                ->bind(':inicio',       $data['inicio'])
                ->bind(':fin',          $data['fin'])
                ->bind(':comentario',   $data['comentario'])
                ->bind(':activo',       $data['activo'])
                ->bind(':id_recurso',   $data['id_recurso'])
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la reserva permanente");
        }
    }

    /**
     * Eliminar reserva permanente
     */
    public function delete(int $id): void
    {
        try {
            $this->db
                ->query("DELETE FROM Reserva_permanente WHERE id_reserva_espacio = :id")
                ->bind(':id', $id)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar la reserva permanente");
        }
    }
}
