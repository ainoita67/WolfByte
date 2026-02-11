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
     * Obtener reservas de un espacio concreto
     */
    public function getByEspacio(String $idEspacio): array
    {
        try {
            // Selecciona los datos de reserva y actividad asociada al espacio
            $sql = "
                SELECT r.id_reserva, r.asignatura, r.autorizada, r.observaciones,
                       r.grupo, r.profesor, r.inicio, r.fin, re.actividad
                FROM Reserva r
                JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                WHERE re.id_espacio = :idEspacio
                ORDER BY r.inicio ASC
            ";

            return $this->db
                ->query($sql)
                ->bind(':idEspacio', $idEspacio)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del espacio");
        }
    }
}
