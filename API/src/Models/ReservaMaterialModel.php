<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaMaterialModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener reservas de un material concreto
     */
    public function getByMaterial(String $idMaterial): array
    {
        try {
            // Selecciona los datos de reserva y actividad asociada al material
            $sql = "SELECT r.id_reserva, r.asignatura, r.autorizada, r.observaciones, r.grupo, r.profesor, r.inicio, r.fin, re.usaenespacio, re.unidades
                FROM Reserva r
                JOIN Reserva_Portatiles re ON r.id_reserva = re.id_reserva_material
                WHERE re.id_material = :idMaterial
                ORDER BY r.inicio ASC
            ";

            return $this->db
                ->query($sql)
                ->bind(':idMaterial', $idMaterial)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del material");
        }
    }
}
