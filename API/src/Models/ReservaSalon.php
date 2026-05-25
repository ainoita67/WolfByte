<?php
declare(strict_types=1);

namespace Models;

use PDO;

class Reserva_EspacioModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $sql = "
            SELECT 
                r.id_reserva,
                r.asignatura,
                r.profesor,
                r.grupo,
                r.inicio,
                r.fin,
                re.id_reserva_espacio,
                re.actividad,
                re.id_espacio,
                n.nombre AS necesidad
            FROM reserva r
            INNER JOIN reserva_espacio re ON r.id_reserva = re.id_reserva
            LEFT JOIN necesidad_R_espacio nre ON re.id_reserva_espacio = nre.id_reserva_espacio
            LEFT JOIN necesidad n ON nre.id_necesidad = n.id_necesidad
            ORDER BY r.inicio
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
