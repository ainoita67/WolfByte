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
                    r.id_reserva,
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
                ->fetchAll();
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
    public function update(int $id, array $data): array
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
            return $this->findById($id);
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
                ->query("
                    SELECT
                        m.unidades AS materialunidades,
                        (
                            SELECT COALESCE((SUM(rp.unidades)-SUM(lp.unidades)),0)
                            FROM Reserva r
                            JOIN Reserva_Portatiles rp 
                                ON rp.id_reserva_material = r.id_reserva
                            LEFT JOIN Liberacion_puntual lp
                                ON r.id_reserva=lp.id_reserva
                            WHERE rp.id_material = m.id_material
                            AND rp.id_reserva_material != :id
                            AND r.inicio <= :fin1
                            AND r.fin >= :inicio1
                            AND r.autorizada!=0
                        )
                        +
                        (
                            SELECT COALESCE(SUM(rep.unidades),0)
                            FROM Reserva_permanente rep
                            WHERE rep.id_recurso = m.id_material
                            AND rep.dia_semana = :diasemana1
                            AND rep.activo = 1
                            AND rep.inicio <= :horafin1
                            AND rep.fin >= :horainicio1
                        )
                        -
                        (
                            SELECT COALESCE(SUM(lp.unidades),0)
                            FROM Liberacion_puntual lp
                            JOIN Reserva_permanente rep 
                                ON rep.id_reserva_permanente = lp.id_reserva_permanente
                                AND rep.id_recurso = m.id_material
                                AND rep.dia_semana = :diasemana2
                                AND rep.activo = 1
                            WHERE lp.inicio <= :fin2
                            AND lp.fin >= :inicio2
                        )
                    AS totalunidades
                    FROM Material m
                    WHERE m.id_material = :material
                ")
                ->bind(':diasemana1', $diasemana)
                ->bind(':diasemana2', $diasemana)
                ->bind(':horainicio1', $horainicio)
                ->bind(':horafin1', $horafin)
                ->bind(':inicio1', $data['inicio'])
                ->bind(':fin1', $data['fin'])
                ->bind(':inicio2', $data['inicio'])
                ->bind(':fin2', $data['fin'])
                ->bind(':material', $data['id_material'])
                ->bind(':id', $id)
                ->fetch();
            if(($filas['materialunidades']-$filas['totalunidades'])<$data['unidades']){
                return false;
            }
            return true;
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage());
            throw new \Exception("Error al crear o actualizar reservas del portátil");
        }
    }
}