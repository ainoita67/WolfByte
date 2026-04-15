<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaPortatilModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener reservas
     */
    public function getAll(): array
    {
        try{
            return $this->db
                ->query("
                    SELECT
                        re.id_reserva,
                        re.asignatura,
                        re.profesor,
                        re.grupo,
                        re.inicio,
                        re.fin,
                        re.observaciones,
                        re.autorizada,
                        re.f_creacion,
                        re.id_usuario,
                        re.id_usuario_autoriza,
                        re.descripcion,
                        r.activo,
                        r.especial,
                        re.planta,
                        e.id_edificio,
                        e.nombre_edificio,
                        rp.unidades,
                        rp.id_material,
                        rp.usaenespacio,
                        p.numero_planta,
                        p.nombre_planta
                    FROM Reserva re
                    JOIN Reserva_Portatiles rp ON re.id_reserva = rp.id_reserva_material
                    JOIN Material m ON rp.id_material = m.id_material
                    JOIN Recurso r ON m.id_material = r.id_recurso
                    JOIN Edificio e ON r.id_edificio = e.id_edificio
                    JOIN Planta p ON r.numero_planta = p.numero_planta AND r.id_edificio = p.id_edificio
                    ORDER BY r.inicio;
                ")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas portátil");
        }
    }

    public function findById(int $id): array
    {
        try{
            return $this->db
                ->query("
                    SELECT
                        r.id_reserva,
                        r.autorizada,
                        r.asignatura,
                        r.profesor,
                        r.grupo,
                        r.inicio,
                        r.fin,
                        rp.unidades,
                        rp.id_material,
                        rp.usaenespacio
                    FROM Reserva r
                    JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    WHERE rp.id_reserva_material=:id
                    ORDER BY r.inicio;
                ")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas de portátil");
        }
    }

    /**
     * Obtener reservas de un portatil concreto
     */
    public function getByPortatil(string $idportatil): array
    {
        try{
            return $this->db
                ->query("
                    SELECT
                        r.id_reserva,
                        r.autorizada,
                        r.asignatura,
                        r.profesor,
                        r.grupo,
                        r.inicio,
                        r.fin,
                        rp.unidades,
                        rp.id_material,
                        rp.usaenespacio
                    FROM Reserva r
                    JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    WHERE rp.id_material=:portatil
                    ORDER BY r.inicio;
                ")
                ->bind(':portatil', $idportatil)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del portátil");
        }
    }

    /**
     * Crear reservas
     */
    public function create(array $data): array
    {
        try{
            $this->db
                ->query("
                    INSERT INTO Reserva_Portatiles (id_reserva_material, unidades, id_material, usaenespacio)
                    VALUES (:id_reserva_material, :unidades, :id_material, :usaenespacio)
                ")
                ->bind(':id_reserva_material', $data['id_reserva_material'])
                ->bind(':unidades', $data['unidades'])
                ->bind(':id_material', $data['id_material'])
                ->bind(':usaenespacio', $data['usaenespacio'])
                ->execute();
            return $this->findById((int)$data['id_reserva_material']);
        } catch (PDOException $e) {
            throw new \Exception("Error al crear reservas del portátil");
        }
    }

    /**
     * Actualizar reservas
     */
    public function update(int $id, array $data): bool
    {
        try{
            $this->db
                ->query("
                    UPDATE Reserva_Portatiles SET
                    unidades=:unidades,
                    id_material=:id_material,
                    usaenespacio=:usaenespacio
                    WHERE id_reserva_material=:id
                ")
                ->bind(':unidades', $data['unidades'])
                ->bind(':id_material', $data['id_material'])
                ->bind(':usaenespacio', $data['usaenespacio'])
                ->bind(':id', $id)
                ->execute();

            return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'] > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar reservas del portátil");
        }
    }

    public function getReservaFecha(int $id, array $data): bool
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
                ->bind(':material1', $data['id_material'])
                ->bind(':material2', $data['id_material'])
                ->bind(':material3', $data['id_material'])
                ->bind(':id', $id)
                ->fetch();
            if(($filas['materialunidades']-$filas['totalunidades'])<$data['unidades']){
                return false;
            }
            return true;
        } catch (PDOException $e) {
            throw new \Exception("Error al crear o actualizar reservas del portátil");
        }
    }
}