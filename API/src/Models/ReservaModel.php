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

   public function updateFechas(
    int $idReserva,
    string $inicio,
    string $fin
): void {
    $this->db
        ->query("
            UPDATE Reserva
            SET inicio = :inicio,
                fin = :fin
            WHERE id_reserva = :id
        ")
        ->bind(':inicio', $inicio)
        ->bind(':fin', $fin)
        ->bind(':id', $idReserva)
        ->execute();
}

public function getReservasSalonActos(): array
{
    return $this->db
        ->query("
            SELECT r.*, u.nombre, u.apellidos 
            FROM Reserva r
            JOIN Usuario u ON r.id_usuario = u.id_usuario
            WHERE r.id_aula = 1  -- Ajusta el ID del salón de actos
            ORDER BY r.inicio
        ")
        ->fetchAll();
}

}