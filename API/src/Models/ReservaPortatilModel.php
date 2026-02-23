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
     * Obtener todas las reservas de portátiles
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        r.id_reserva,
                        r.asignatura as aula,
                        r.profesor,
                        r.grupo,
                        DATE_FORMAT(r.f_creacion, '%Y-%m-%d %H:%i:%s') as f_creacion,
                        DATE_FORMAT(r.inicio, '%Y-%m-%d %H:%i:%s') as inicio,
                        DATE_FORMAT(r.fin, '%Y-%m-%d %H:%i:%s') as fin,
                        r.autorizada,
                        u.nombre as usuario,
                        u.id_usuario,
                        rp.unidades as num_portatiles,
                        rp.id_material as carro,
                        rp.usaenespacio as espacio,
                        rec.descripcion as descripcion_carro,
                        ed.nombre_edificio as edificio,
                        rec.numero_planta as planta
                    FROM Reserva r
                    INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    INNER JOIN Usuario u ON r.id_usuario = u.id_usuario
                    INNER JOIN Material m ON rp.id_material = m.id_material
                    INNER JOIN Recurso rec ON m.id_material = rec.id_recurso
                    LEFT JOIN Edificio ed ON rec.id_edificio = ed.id_edificio
                    WHERE r.tipo = 'Reserva_material'
                    ORDER BY r.inicio DESC
                ")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas de portátiles: " . $e->getMessage());
        }
    }

    /**
     * Obtener reserva por ID
     */
    public function findById(int $id): ?array
    {
        try {
            $result = $this->db
                ->query("
                    SELECT 
                        r.id_reserva,
                        r.asignatura as aula,
                        r.profesor,
                        r.grupo,
                        DATE_FORMAT(r.f_creacion, '%Y-%m-%d %H:%i:%s') as f_creacion,
                        DATE_FORMAT(r.inicio, '%Y-%m-%d %H:%i:%s') as inicio,
                        DATE_FORMAT(r.fin, '%Y-%m-%d %H:%i:%s') as fin,
                        r.autorizada,
                        u.nombre as usuario,
                        u.id_usuario,
                        rp.unidades as num_portatiles,
                        rp.id_material as carro,
                        rp.usaenespacio as espacio,
                        rec.descripcion as descripcion_carro,
                        ed.nombre_edificio as edificio,
                        rec.numero_planta as planta,
                        rec.id_edificio
                    FROM Reserva r
                    INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    INNER JOIN Usuario u ON r.id_usuario = u.id_usuario
                    INNER JOIN Material m ON rp.id_material = m.id_material
                    INNER JOIN Recurso rec ON m.id_material = rec.id_recurso
                    LEFT JOIN Edificio ed ON rec.id_edificio = ed.id_edificio
                    WHERE r.id_reserva = :id AND r.tipo = 'Reserva_material'
                ")
                ->bind(':id', $id)
                ->fetch();

            return $result ?: null;
            
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la reserva: " . $e->getMessage());
        }
    }

    /**
     * Obtener reservas por material (portátil)
     */
    public function findByMaterial(string $idMaterial): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        r.id_reserva,
                        r.asignatura as aula,
                        r.profesor,
                        r.grupo,
                        DATE_FORMAT(r.f_creacion, '%Y-%m-%d %H:%i:%s') as f_creacion,
                        DATE_FORMAT(r.inicio, '%Y-%m-%d %H:%i:%s') as inicio,
                        DATE_FORMAT(r.fin, '%Y-%m-%d %H:%i:%s') as fin,
                        r.autorizada,
                        u.nombre as usuario,
                        rp.unidades as num_portatiles,
                        rp.usaenespacio as espacio
                    FROM Reserva r
                    INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    INNER JOIN Usuario u ON r.id_usuario = u.id_usuario
                    WHERE rp.id_material = :id_material AND r.tipo = 'Reserva_material'
                    ORDER BY r.inicio DESC
                ")
                ->bind(':id_material', $idMaterial)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del material: " . $e->getMessage());
        }
    }

    /**
     * Obtener reservas por usuario
     */
    public function findByUsuario(int $idUsuario): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        r.id_reserva,
                        r.asignatura as aula,
                        r.profesor,
                        r.grupo,
                        DATE_FORMAT(r.f_creacion, '%Y-%m-%d %H:%i:%s') as f_creacion,
                        DATE_FORMAT(r.inicio, '%Y-%m-%d %H:%i:%s') as inicio,
                        DATE_FORMAT(r.fin, '%Y-%m-%d %H:%i:%s') as fin,
                        r.autorizada,
                        rp.unidades as num_portatiles,
                        rp.id_material as carro,
                        rp.usaenespacio as espacio,
                        rec.descripcion as descripcion_carro
                    FROM Reserva r
                    INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    INNER JOIN Material m ON rp.id_material = m.id_material
                    INNER JOIN Recurso rec ON m.id_material = rec.id_recurso
                    WHERE r.id_usuario = :id_usuario AND r.tipo = 'Reserva_material'
                    ORDER BY r.inicio DESC
                ")
                ->bind(':id_usuario', $idUsuario)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del usuario: " . $e->getMessage());
        }
    }

    /**
     * Verificar disponibilidad de unidades
     */
    public function checkDisponibilidad(string $idMaterial, string $inicio, string $fin, int $unidades, ?int $excluirId = null): int
    {
        try {
            $query = "
                SELECT COALESCE(SUM(rp.unidades), 0) as total_reservado
                FROM Reserva r
                INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                WHERE rp.id_material = :id_material
                AND r.tipo = 'Reserva_material'
                AND (
                    (r.inicio < :fin AND r.fin > :inicio)
                )
            ";
            
            if ($excluirId) {
                $query .= " AND r.id_reserva != :excluir_id";
            }
            
            $stmt = $this->db->query($query)
                ->bind(':id_material', $idMaterial)
                ->bind(':inicio', $inicio)
                ->bind(':fin', $fin);
            
            if ($excluirId) {
                $stmt->bind(':excluir_id', $excluirId);
            }
            
            $result = $stmt->fetch();
            
            return (int)($result['total_reservado'] ?? 0);
        } catch (PDOException $e) {
            throw new \Exception("Error al verificar disponibilidad: " . $e->getMessage());
        }
    }

    /**
     * Obtener unidades totales de un material
     */
    public function getMaterialUnidades(string $idMaterial): int
    {
        try {
            $result = $this->db
                ->query("SELECT unidades FROM Material WHERE id_material = :id_material")
                ->bind(':id_material', $idMaterial)
                ->fetch();
            
            return $result ? (int)$result['unidades'] : 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener unidades del material: " . $e->getMessage());
        }
    }

    /**
     * Crear una nueva reserva de portátiles
     */
    public function create(array $data): int
    {
        try {
            $this->db->beginTransaction();

            // Insertar en tabla Reserva
            $this->db
                ->query("
                    INSERT INTO Reserva 
                    (asignatura, autorizada, grupo, profesor, f_creacion, inicio, fin, id_usuario, tipo) 
                    VALUES 
                    (:asignatura, :autorizada, :grupo, :profesor, NOW(), :inicio, :fin, :id_usuario, 'Reserva_material')
                ")
                ->bind(':asignatura', $data['aula'])
                ->bind(':autorizada', $data['autorizada'] ?? 0)
                ->bind(':grupo', $data['grupo'])
                ->bind(':profesor', $data['profesor'])
                ->bind(':inicio', $data['fecha'] . ' ' . $data['hora_inicio'] . ':00')
                ->bind(':fin', $data['fecha'] . ' ' . $data['hora_fin'] . ':00')
                ->bind(':id_usuario', $data['id_usuario'])
                ->execute();

            $idReserva = (int)$this->db->lastId();

            // Insertar en tabla Reserva_Portatiles
            $this->db
                ->query("
                    INSERT INTO Reserva_Portatiles 
                    (id_reserva_material, unidades, id_material, usaenespacio) 
                    VALUES 
                    (:id_reserva, :unidades, :id_material, :usaenespacio)
                ")
                ->bind(':id_reserva', $idReserva)
                ->bind(':unidades', $data['num_portatiles'])
                ->bind(':id_material', $data['carro'])
                ->bind(':usaenespacio', $data['espacio'])
                ->execute();

            $this->db->commit();
            return $idReserva;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al crear la reserva: " . $e->getMessage());
        }
    }

    /**
     * Actualizar una reserva completa
     */
    public function update(int $id, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // Actualizar tabla Reserva
            $this->db
                ->query("
                    UPDATE Reserva 
                    SET asignatura = :asignatura,
                        grupo = :grupo,
                        profesor = :profesor,
                        inicio = :inicio,
                        fin = :fin,
                        autorizada = :autorizada
                    WHERE id_reserva = :id_reserva
                ")
                ->bind(':asignatura', $data['aula'])
                ->bind(':grupo', $data['grupo'])
                ->bind(':profesor', $data['profesor'])
                ->bind(':inicio', $data['fecha'] . ' ' . $data['hora_inicio'] . ':00')
                ->bind(':fin', $data['fecha'] . ' ' . $data['hora_fin'] . ':00')
                ->bind(':autorizada', $data['autorizada'] ?? 0)
                ->bind(':id_reserva', $id)
                ->execute();

            // Actualizar tabla Reserva_Portatiles
            $this->db
                ->query("
                    UPDATE Reserva_Portatiles 
                    SET unidades = :unidades,
                        id_material = :id_material,
                        usaenespacio = :usaenespacio
                    WHERE id_reserva_material = :id_reserva
                ")
                ->bind(':unidades', $data['num_portatiles'])
                ->bind(':id_material', $data['carro'])
                ->bind(':usaenespacio', $data['espacio'])
                ->bind(':id_reserva', $id)
                ->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al actualizar la reserva: " . $e->getMessage());
        }
    }

    /**
     * Actualizar solo las fechas
     */
    public function updateFechas(int $id, string $fecha, string $hora_inicio, string $hora_fin): bool
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva 
                    SET inicio = :inicio,
                        fin = :fin
                    WHERE id_reserva = :id_reserva
                ")
                ->bind(':inicio', $fecha . ' ' . $hora_inicio . ':00')
                ->bind(':fin', $fecha . ' ' . $hora_fin . ':00')
                ->bind(':id_reserva', $id)
                ->execute();

            return $this->db->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar fechas: " . $e->getMessage());
        }
    }

    /**
     * Actualizar solo las unidades
     */
    public function updateUnidades(int $id, int $unidades): bool
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva_Portatiles 
                    SET unidades = :unidades
                    WHERE id_reserva_material = :id_reserva
                ")
                ->bind(':unidades', $unidades)
                ->bind(':id_reserva', $id)
                ->execute();

            return $this->db->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar unidades: " . $e->getMessage());
        }
    }

    /**
     * Eliminar una reserva
     */
    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Eliminar de Reserva_Portatiles primero
            $this->db
                ->query("DELETE FROM Reserva_Portatiles WHERE id_reserva_material = :id_reserva")
                ->bind(':id_reserva', $id)
                ->execute();

            // Eliminar de Reserva
            $this->db
                ->query("DELETE FROM Reserva WHERE id_reserva = :id_reserva")
                ->bind(':id_reserva', $id)
                ->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al eliminar la reserva: " . $e->getMessage());
        }
    }
}