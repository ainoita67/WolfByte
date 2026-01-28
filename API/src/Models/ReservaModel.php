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
}
