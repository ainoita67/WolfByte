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
            throw new \Exception("Error al obtener reservas portátil");
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
}