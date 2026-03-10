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
                    re.actividad,
                    re.id_espacio,
                    GROUP_CONCAT(n.nombre) AS necesidades
                FROM Reserva r
                JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                LEFT JOIN Necesidad_R_espacio nre ON re.id_reserva = nre.id_reserva_espacio
                LEFT JOIN Necesidad n ON nre.id_necesidad = n.id_necesidad
                GROUP BY r.id_reserva
                ORDER BY r.inicio;
            ")
            ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas espacio");
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
                        re.actividad,
                        re.id_espacio,
                        GROUP_CONCAT(n.nombre) AS necesidades
                    FROM Reserva r
                    JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                    LEFT JOIN Necesidad_R_espacio nre ON re.id_reserva = nre.id_reserva_espacio
                    LEFT JOIN Necesidad n ON nre.id_necesidad = n.id_necesidad
                    WHERE re.id_reserva=:id
                    GROUP BY r.id_reserva
                    ORDER BY r.inicio;
                ")
                ->bind(':id', $id)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas espacio");
        }
    }

    /**
     * Obtener reservas de un espacio concreto
     */
    public function getByEspacio(string $idEspacio): array
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
                        re.actividad,
                        re.id_espacio,
                        GROUP_CONCAT(n.nombre) AS necesidades
                    FROM Reserva r
                    JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                    LEFT JOIN Necesidad_R_espacio nre ON re.id_reserva = nre.id_reserva_espacio
                    LEFT JOIN Necesidad n ON nre.id_necesidad = n.id_necesidad
                    WHERE re.id_espacio=:espacio
                    GROUP BY r.id_reserva
                    ORDER BY r.inicio;
                ")
                ->bind(':espacio', $idEspacio)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del espacio");
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
                    INSERT INTO Reserva_espacio (id_reserva, actividad, id_espacio)
                    VALUES (:id_reserva, :actividad, :id_espacio)
                ")
                ->bind(':id_reserva', $data['id_reserva'])
                ->bind(':actividad', $data['actividad'])
                ->bind(':id_espacio', $data['id_espacio'])
                ->execute();
            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar reservas del espacio");
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
                    UPDATE Reserva_espacio SET
                    actividad=:actividad,
                    id_espacio=:id_espacio
                    WHERE id_reserva=:id
                ")
                ->bind(':actividad', $data['actividad'])
                ->bind(':id_espacio', $data['id_espacio'])
                ->bind(':id', $id)
                ->execute();
            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar reservas del espacio");
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

            $filas=$this->db
                ->query("
                    SELECT
                        COUNT(DISTINCT r.id_reserva)+COUNT(DISTINCT rep.id_reserva_permanente)-COUNT(DISTINCT lp.id_liberacion_puntual) AS totalreservas
                    FROM Espacio e
                    LEFT JOIN Reserva_permanente rep ON rep.id_recurso=e.id_espacio AND rep.dia_semana=:diasemana AND rep.activo=1
                    AND ((rep.inicio>:horainicio1 AND rep.fin<:horainicio2) OR (rep.inicio>:horafin1 AND rep.fin<:horafin2) OR (rep.inicio<=:horainicio3 AND rep.fin>=:horafin3))
                    LEFT JOIN Liberacion_puntual lp ON lp.id_reserva_permanente=rep.id_reserva_permanente
                    AND ((lp.inicio>:inicio1 AND lp.fin<:inicio2) OR (lp.inicio>:fin1 AND lp.fin<:fin2) OR (lp.inicio<=:inicio3 AND lp.fin>=:fin3))
                    LEFT JOIN Reserva_espacio re ON re.id_espacio=e.id_espacio AND re.id_espacio=:espacio AND re.id_reserva!=:id
                    LEFT JOIN Reserva r ON r.id_reserva=re.id_reserva
                    AND ((r.inicio>:inicio4 AND r.fin<:inicio5) OR (r.inicio>:fin4 AND r.fin<:fin5) OR (r.inicio<=:inicio6 AND r.fin>=:fin6))
                ")
                ->bind(':diasemana', $diasemana)
                ->bind(':horainicio1', $horainicio)
                ->bind(':horafin1', $horafin)
                ->bind(':horainicio2', $horainicio)
                ->bind(':horafin2', $horafin)
                ->bind(':horainicio3', $horainicio)
                ->bind(':horafin3', $horafin)
                ->bind(':inicio1', $data['inicio'])
                ->bind(':fin1', $data['fin'])
                ->bind(':inicio2', $data['inicio'])
                ->bind(':fin2', $data['fin'])
                ->bind(':inicio3', $data['inicio'])
                ->bind(':fin3', $data['fin'])
                ->bind(':inicio4', $data['inicio'])
                ->bind(':fin4', $data['fin'])
                ->bind(':inicio5', $data['inicio'])
                ->bind(':fin5', $data['fin'])
                ->bind(':inicio6', $data['inicio'])
                ->bind(':fin6', $data['fin'])
                ->bind(':espacio', $data['id_espacio'])
                ->bind(':id', $id)
                ->fetch();
            if($filas['totalreservas']>0){
                return false;
            }
            return true;
        } catch (PDOException $e) {
            throw new \Exception("Error al crear o actualizar reservas del espacio");
        }
    }
}