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
            SELECT 
                r.id_reserva,
                r.asignatura,
                r.autorizada,
                r.observaciones,
                r.grupo,
                r.profesor,
                r.f_creacion,
                r.inicio,
                r.fin,
                r.id_usuario,
                r.id_usuario_autoriza,
                r.tipo,
                u.nombre,
                u.apellidos,
                re.actividad,
                re.id_espacio
            FROM Reserva r
            JOIN Usuario u ON r.id_usuario = u.id_usuario
            JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
            WHERE re.id_espacio = 'salon'
            ORDER BY r.inicio
        ")
        ->fetchAll();
}

}