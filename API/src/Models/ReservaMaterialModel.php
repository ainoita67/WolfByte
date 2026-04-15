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
                    p.nombre_planta,
                    rp.unidades AS unidades_libres
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


    public function getUnidadesFecha(string $idMaterial, array $data): int
    {
        try{
            $horainicio=explode(' ', $data['inicio'])[1];
            $horafin=explode(' ', $data['fin'])[1];
            $diasemana=date('w', strtotime($data['inicio']));
            if($diasemana!=date('w', strtotime($data['fin']))){
                return false;
            }

            //Suma unidades de reservas y de reservas permanentes y resta de liberaciones
            $filas=$this->db
                ->query("SELECT
                        m.unidades AS materialunidades,
                            (SELECT IFNULL(SUM(rp.unidades),0)
                            FROM Reserva r
                            JOIN Reserva_Portatiles rp ON r.id_reserva=rp.id_reserva_material
                            JOIN Material m ON rp.id_material=m.id_material
                            WHERE r.tipo='Reserva_material' AND m.id_material=:material1
                            AND ((r.inicio>:fin1 AND r.fin<:inicio1)
                            OR (r.inicio=:inicio2 AND r.fin=:fin2))
                            AND r.id_reserva!=:id)
                            +
                            (SELECT IFNULL(SUM(rp.unidades),0)-IFNULL(SUM(lp.unidades),0)
                            FROM Reserva_permanente rp
                            LEFT JOIN Liberacion_puntual lp ON rp.id_reserva_permanente=lp.id_reserva_permanente
                            WHERE rp.id_recurso=:material2 AND rp.activo=1 AND rp.dia_semana=:diasemana
                            AND rp.inicio<:horafin AND rp.fin>:horainicio)
                        AS totalunidades
                    FROM Material m
                    WHERE m.id_material=:material3
                ")
                ->bind(':diasemana', $diasemana)
                ->bind(':horainicio', $horainicio)
                ->bind(':horafin', $horafin)
                ->bind(':inicio1', $data['inicio'])
                ->bind(':fin1', $data['fin'])
                ->bind(':inicio2', $data['inicio'])
                ->bind(':fin2', $data['fin'])
                ->bind(':material1', $idMaterial)
                ->bind(':material2', $idMaterial)
                ->bind(':material3', $idMaterial)
                ->bind(':id', $data['id_reserva'])
                ->fetch();
            
            return $filas['materialunidades']-$filas['totalunidades'];
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
            throw new \Exception("Error al crear o actualizar reservas del portátil");
        }
    }
}