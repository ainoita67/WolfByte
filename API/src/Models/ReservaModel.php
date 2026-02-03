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
     * Crear una nueva reserva principal
     * inicio y fin son opcionales, autorizada por defecto 0
     */
    public function create(array $data): array
    {
        try {
            $this->db->query("
                INSERT INTO Reserva (
                    inicio,
                    fin,
                    id_usuario,
                    autorizada,
                    grupo
                ) VALUES (
                    :inicio,
                    :fin,
                    :id_usuario,
                    :autorizada,
                    :grupo
                )
            ")
            ->bind(':inicio', $data['inicio'] ?? null)
            ->bind(':fin', $data['fin'] ?? null)
            ->bind(':id_usuario', $data['id_usuario'])
            ->bind(':autorizada', $data['autorizada'] ?? 0)
            ->bind(':grupo', $data['grupo'] ?? '') // <--- aquí
            ->execute();


            $id = $this->db->lastInsertId();
            return $this->findById((int)$id);
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la reserva: " . $e->getMessage());
        }
    }

    /**
     * Obtener reservas por usuario
     */
    public function getByUsuario(int $idUsuario): array
    {
        try {
            return $this->db
                ->query("
                    SELECT *
                    FROM Reserva
                    WHERE id_usuario = :id_usuario
                    ORDER BY inicio DESC
                ")
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
        return $this->db
            ->query("SELECT * FROM Reserva WHERE id_reserva = :id")
            ->bind(':id', $id)
            ->fetch();
    }

    /**
     * Actualizar reserva (opcional)
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db->query("
                UPDATE Reserva
                SET inicio = :inicio,
                    fin = :fin,
                    id_usuario = :id_usuario,
                    autorizada = :autorizada
                WHERE id_reserva = :id
            ")
            ->bind(':inicio', $data['inicio'] ?? null)
            ->bind(':fin', $data['fin'] ?? null)
            ->bind(':id_usuario', $data['id_usuario'])
            ->bind(':autorizada', $data['autorizada'] ?? 0)
            ->bind(':id', $id)
            ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la reserva: " . $e->getMessage());
        }
    }

    /**
     * Eliminar una reserva
     */
    public function delete(int $id): void
    {
        try {
            $this->db->query("DELETE FROM Reserva WHERE id_reserva = :id")
                ->bind(':id', $id)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar la reserva: " . $e->getMessage());
        }
    }
}
