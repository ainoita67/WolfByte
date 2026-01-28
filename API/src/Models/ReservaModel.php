<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todas las reservas
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva ORDER BY inicio DESC")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener todas las reservas");
        }
    }

    /**
     * Obtener reservas por usuario
     */
    public function getByUsuario(int $idUsuario): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva WHERE id_usuario = :id_usuario ORDER BY inicio DESC")
                ->bind(':id_usuario', $idUsuario)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del usuario");
        }
    }

    /**
     * Obtener una reserva por ID
     */
    public function findById(int $id): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva WHERE id_reserva = :id")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener la reserva");
        }
    }

    /**
     * Crear una nueva reserva
     */
    public function create(array $data): array
    {
        try {
            $this->db->query("
                INSERT INTO Reserva (id_usuario, inicio, fin, detalle)
                VALUES (:id_usuario, :inicio, :fin, :detalle)
            ")
            ->bind(':id_usuario', $data['id_usuario'])
            ->bind(':inicio', $data['inicio'])
            ->bind(':fin', $data['fin'])
            ->bind(':detalle', $data['detalle'] ?? null)
            ->execute();

            // Devolver la reserva recién creada
            return $this->findById((int)$this->db->lastInsertId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la reserva");
        }
    }

    /**
     * Actualizar una reserva por ID
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db->query("
                UPDATE Reserva
                SET inicio = :inicio,
                    fin = :fin,
                    detalle = :detalle
                WHERE id_reserva = :id
            ")
            ->bind(':inicio', $data['inicio'])
            ->bind(':fin', $data['fin'])
            ->bind(':detalle', $data['detalle'] ?? null)
            ->bind(':id', $id)
            ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la reserva");
        }
    }

    /**
     * Eliminar una reserva por ID
     */
    public function delete(int $id): void
    {
        try {
            $this->db
                ->query("DELETE FROM Reserva WHERE id_reserva = :id")
                ->bind(':id', $id)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar la reserva");
        }
    }
}
