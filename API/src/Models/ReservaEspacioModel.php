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

    public function getReservaFecha(int $id, array $data): int
    {
        try{
            $filas=$this->db
                ->query("
                    SELECT
                        COUNT(IFNULL(r.id_reserva,0)) AS total
                    FROM Reserva r JOIN Reserva_espacio re ON r.id_reserva=re.id_reserva
                    WHERE (r.inicio>:inicio1 AND r.fin<:inicio2) OR (r.inicio>:fin1 AND r.fin<:fin2) OR (r.inicio<=:inicio3 AND r.fin>=:fin3)
                    AND re.id_espacio=:espacio AND r.id_reserva!=:id
                ")
                ->bind(':inicio1', $data['inicio'])
                ->bind(':fin1', $data['fin'])
                ->bind(':inicio2', $data['inicio'])
                ->bind(':fin2', $data['fin'])
                ->bind(':inicio3', $data['inicio'])
                ->bind(':fin3', $data['fin'])
                ->bind(':espacio', $data['id_espacio'])
                ->bind(':id', $id)
                ->fetch();
            return (int) $filas['total'];
        } catch (PDOException $e) {
            throw new \Exception("Error al crear o actualizar reservas del espacio");
        }
    }
}