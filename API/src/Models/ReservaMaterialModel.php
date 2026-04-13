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
            $sql = "SELECT
                    DISTINCT re.id_reserva,
                    re.asignatura,
                    re.autorizada,
                    re.observaciones,
                    re.grupo,
                    re.profesor,
                    re.inicio,
                    re.fin,
                    re.autorizada,
                    re.f_creacion,
                    re.id_usuario,
                    re.id_usuario_autoriza,
                    rp.usaenespacio,
                    rp.unidades,
                    r.id_recurso,
                    r.activo,
                    r.especial,
                    r.descripcion,
                    e.id_edificio,
                    e.nombre_edificio,
                    p.numero_planta,
                    p.nombre_planta
                FROM Reserva re
                JOIN Reserva_Portatiles rp ON re.id_reserva = rp.id_reserva_material
                JOIN Material m ON rp.id_material = m.id_material
                JOIN Recurso r ON m.id_material = r.id_recurso
                JOIN Edificio e ON r.id_edificio = e.id_edificio
                JOIN Planta p ON r.numero_planta = p.numero_planta AND r.id_edificio
                WHERE rp.id_material = :idMaterial
                ORDER BY re.inicio ASC
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
