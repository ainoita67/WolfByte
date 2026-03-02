<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class PortatilModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * ===========================================
     * MATERIALES (CARROS DE PORTÁTILES)
     * ===========================================
     */

    /**
     * GET /portatiles/materiales
     * Obtener todos los materiales (carros de portátiles)
     */
    public function getAllMateriales(): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        m.id_material as id,
                        m.unidades,
                        r.id_recurso,
                        r.descripcion,
                        r.activo,
                        r.especial,
                        r.numero_planta,
                        r.id_edificio,
                        e.nombre_edificio as edificio,
                        p.nombre_planta as planta
                    FROM Material m
                    INNER JOIN Recurso r ON m.id_material = r.id_recurso
                    LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
                    LEFT JOIN Planta p ON r.numero_planta = p.numero_planta AND r.id_edificio = p.id_edificio
                    WHERE r.tipo = 'Material'
                    ORDER BY r.id_recurso
                ")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener materiales: " . $e->getMessage());
        }
    }

    /**
     * GET /portatiles/materiales/{id}
     * Obtener material por ID
     */
    public function findMaterialById(string $id): ?array
    {
        try {
            $result = $this->db
                ->query("
                    SELECT 
                        m.id_material as id,
                        m.unidades,
                        r.id_recurso,
                        r.descripcion,
                        r.activo,
                        r.especial,
                        r.numero_planta,
                        r.id_edificio,
                        e.nombre_edificio as edificio,
                        p.nombre_planta as planta
                    FROM Material m
                    INNER JOIN Recurso r ON m.id_material = r.id_recurso
                    LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
                    LEFT JOIN Planta p ON r.numero_planta = p.numero_planta AND r.id_edificio = p.id_edificio
                    WHERE m.id_material = :id AND r.tipo = 'Material'
                ")
                ->bind(':id', $id)
                ->fetch();

            return $result ?: null;
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar material: " . $e->getMessage());
        }
    }

    /**
     * POST /portatiles/materiales
     * Crear un nuevo material (carro de portátiles)
     */
    public function createMaterial(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // Insertar en Recurso
            $this->db
                ->query("
                    INSERT INTO Recurso 
                    (id_recurso, descripcion, tipo, activo, especial, numero_planta, id_edificio) 
                    VALUES 
                    (:id_recurso, :descripcion, 'Material', :activo, :especial, :numero_planta, :id_edificio)
                ")
                ->bind(':id_recurso', $data['id_recurso'])
                ->bind(':descripcion', $data['descripcion'])
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->bind(':numero_planta', $data['numero_planta'])
                ->bind(':id_edificio', $data['id_edificio'])
                ->execute();

            // Insertar en Material
            $this->db
                ->query("
                    INSERT INTO Material (id_material, unidades) 
                    VALUES (:id_material, :unidades)
                ")
                ->bind(':id_material', $data['id_recurso'])
                ->bind(':unidades', $data['unidades'])
                ->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al crear material: " . $e->getMessage());
        }
    }

    /**
     * PUT /portatiles/materiales/{id}
     * Actualizar un material
     */
    public function updateMaterial(string $id, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // Actualizar Recurso
            $this->db
                ->query("
                    UPDATE Recurso 
                    SET descripcion = :descripcion,
                        activo = :activo,
                        especial = :especial
                    WHERE id_recurso = :id_recurso
                ")
                ->bind(':descripcion', $data['descripcion'])
                ->bind(':activo', $data['activo'])
                ->bind(':especial', $data['especial'] ?? 0)
                ->bind(':id_recurso', $id)
                ->execute();

            // Actualizar Material
            $this->db
                ->query("
                    UPDATE Material 
                    SET unidades = :unidades
                    WHERE id_material = :id_material
                ")
                ->bind(':unidades', $data['unidades'])
                ->bind(':id_material', $id)
                ->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al actualizar material: " . $e->getMessage());
        }
    }

    /**
     * DELETE /portatiles/materiales/{id}
     * Eliminar un material
     */
    public function deleteMaterial(string $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Verificar si tiene reservas asociadas
            $reservas = $this->db
                ->query("SELECT COUNT(*) as count FROM Reserva_Portatiles WHERE id_material = :id_material")
                ->bind(':id_material', $id)
                ->fetch();
            
            if ($reservas && $reservas['count'] > 0) {
                throw new \Exception("No se puede eliminar el material porque tiene reservas asociadas");
            }

            // Eliminar de Material primero
            $this->db
                ->query("DELETE FROM Material WHERE id_material = :id_material")
                ->bind(':id_material', $id)
                ->execute();

            // Eliminar de Recurso
            $this->db
                ->query("DELETE FROM Recurso WHERE id_recurso = :id_recurso")
                ->bind(':id_recurso', $id)
                ->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al eliminar material: " . $e->getMessage());
        }
    }

    /**
     * Verificar si un material existe
     */
    public function materialExists(string $id): bool
    {
        try {
            $result = $this->db
                ->query("SELECT COUNT(*) as count FROM Material WHERE id_material = :id")
                ->bind(':id', $id)
                ->fetch();
            
            return $result && $result['count'] > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al verificar material: " . $e->getMessage());
        }
    }

    /**
     * ===========================================
     * RESERVAS DE PORTÁTILES
     * ===========================================
     */

    /**
     * GET /portatiles/reservas
     * Obtener todas las reservas de portátiles
     */
    public function getAllReservas(): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        r.id_reserva,
                        r.asignatura,
                        r.grupo,
                        r.profesor,
                        r.f_creacion,
                        r.inicio,
                        r.fin,
                        r.autorizada,
                        r.observaciones,
                        r.id_usuario,
                        u.nombre as nombre_usuario,
                        u.correo,
                        rp.unidades,
                        rp.id_material,
                        rp.usaenespacio,
                        rec.descripcion as descripcion_material,
                        e.nombre_edificio as edificio,
                        p.nombre_planta as planta
                    FROM Reserva r
                    INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    INNER JOIN Usuario u ON r.id_usuario = u.id_usuario
                    INNER JOIN Material m ON rp.id_material = m.id_material
                    INNER JOIN Recurso rec ON m.id_material = rec.id_recurso
                    LEFT JOIN Edificio e ON rec.id_edificio = e.id_edificio
                    LEFT JOIN Planta p ON rec.numero_planta = p.numero_planta AND rec.id_edificio = p.id_edificio
                    WHERE r.tipo = 'Reserva_material'
                    ORDER BY r.inicio DESC
                ")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas: " . $e->getMessage());
        }
    }

    /**
     * GET /portatiles/reservas/usuario/{id_usuario}
     * Obtener reservas de portátiles por usuario
     */
    public function getReservasByUsuario(int $idUsuario): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        r.id_reserva,
                        r.asignatura,
                        r.grupo,
                        r.profesor,
                        r.f_creacion,
                        r.inicio,
                        r.fin,
                        r.autorizada,
                        r.observaciones,
                        r.id_usuario,
                        u.nombre as nombre_usuario,
                        u.correo,
                        rp.unidades,
                        rp.id_material,
                        rp.usaenespacio,
                        rec.descripcion as descripcion_material
                    FROM Reserva r
                    INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    INNER JOIN Usuario u ON r.id_usuario = u.id_usuario
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
     * GET /portatiles/reservas/{id}
     * Obtener reserva de portátil por ID de reserva
     */
    public function findReservaById(int $id): ?array
    {
        try {
            $result = $this->db
                ->query("
                    SELECT 
                        r.id_reserva,
                        r.asignatura,
                        r.grupo,
                        r.profesor,
                        r.f_creacion,
                        r.inicio,
                        r.fin,
                        r.autorizada,
                        r.observaciones,
                        r.id_usuario,
                        u.nombre as nombre_usuario,
                        u.correo,
                        rp.unidades,
                        rp.id_material,
                        rp.usaenespacio,
                        rec.descripcion as descripcion_material,
                        e.nombre_edificio as edificio,
                        p.nombre_planta as planta
                    FROM Reserva r
                    INNER JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    INNER JOIN Usuario u ON r.id_usuario = u.id_usuario
                    INNER JOIN Material m ON rp.id_material = m.id_material
                    INNER JOIN Recurso rec ON m.id_material = rec.id_recurso
                    LEFT JOIN Edificio e ON rec.id_edificio = e.id_edificio
                    LEFT JOIN Planta p ON rec.numero_planta = p.numero_planta AND rec.id_edificio = p.id_edificio
                    WHERE r.id_reserva = :id AND r.tipo = 'Reserva_material'
                ")
                ->bind(':id', $id)
                ->fetch();

            return $result ?: null;
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar reserva: " . $e->getMessage());
        }
    }

    /**
     * POST /portatiles/reservas
     * Crear una nueva reserva de portátil
     * 
     * IMPORTANTE: Opción 1 - Con valor por defecto para autorizada
     */
    public function createReserva(array $data): int
    {
        try {
            $this->db->beginTransaction();

            // Asegurar que autorizada tenga un valor por defecto (0) si no viene
            $autorizada = isset($data['autorizada']) ? (int)$data['autorizada'] : 0;

            // Insertar en tabla Reserva
            $this->db
                ->query("
                    INSERT INTO Reserva 
                    (asignatura, autorizada, observaciones, grupo, profesor, f_creacion, inicio, fin, id_usuario, tipo) 
                    VALUES 
                    (:asignatura, :autorizada, :observaciones, :grupo, :profesor, NOW(), :inicio, :fin, :id_usuario, 'Reserva_material')
                ")
                ->bind(':asignatura', $data['asignatura'])
                ->bind(':autorizada', $autorizada)  // Ahora siempre tiene un valor
                ->bind(':observaciones', $data['observaciones'] ?? null)
                ->bind(':grupo', $data['grupo'])
                ->bind(':profesor', $data['profesor'])
                ->bind(':inicio', $data['inicio'])
                ->bind(':fin', $data['fin'])
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
                ->bind(':unidades', $data['unidades'])
                ->bind(':id_material', $data['id_material'])
                ->bind(':usaenespacio', $data['usaenespacio'])
                ->execute();

            $this->db->commit();
            return $idReserva;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al crear reserva: " . $e->getMessage());
        }
    }

    /**
     * POST /portatiles/reservas/disponibilidad
     * Verificar disponibilidad de un portátil en un rango de fechas
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
     * PUT /portatiles/reservas/{id}
     * Actualizar una reserva completa
     */
    public function updateReserva(int $id, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // Asegurar que autorizada tenga un valor
            $autorizada = isset($data['autorizada']) ? (int)$data['autorizada'] : 0;

            // Actualizar tabla Reserva
            $this->db
                ->query("
                    UPDATE Reserva 
                    SET asignatura = :asignatura,
                        grupo = :grupo,
                        profesor = :profesor,
                        inicio = :inicio,
                        fin = :fin,
                        observaciones = :observaciones,
                        autorizada = :autorizada
                    WHERE id_reserva = :id_reserva
                ")
                ->bind(':asignatura', $data['asignatura'])
                ->bind(':grupo', $data['grupo'])
                ->bind(':profesor', $data['profesor'])
                ->bind(':inicio', $data['inicio'])
                ->bind(':fin', $data['fin'])
                ->bind(':observaciones', $data['observaciones'] ?? null)
                ->bind(':autorizada', $autorizada)
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
                ->bind(':unidades', $data['unidades'])
                ->bind(':id_material', $data['id_material'])
                ->bind(':usaenespacio', $data['usaenespacio'])
                ->bind(':id_reserva', $id)
                ->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al actualizar reserva: " . $e->getMessage());
        }
    }

    /**
     * PATCH /portatiles/reservas/{id}
     * Actualizar parcialmente una reserva (solo fechas)
     */
    public function patchFechas(int $id, string $inicio, string $fin): bool
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva 
                    SET inicio = :inicio,
                        fin = :fin
                    WHERE id_reserva = :id_reserva
                ")
                ->bind(':inicio', $inicio)
                ->bind(':fin', $fin)
                ->bind(':id_reserva', $id)
                ->execute();

            return $this->db->rowCount() > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar fechas: " . $e->getMessage());
        }
    }

    /**
     * PATCH /portatiles/reservas/{id}/unidades
     * Actualizar solo el número de unidades de una reserva
     */
    public function patchUnidades(int $id, int $unidades): bool
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
     * DELETE /portatiles/reservas/{id}
     * Eliminar una reserva
     */
    public function deleteReserva(int $id): bool
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
            throw new \Exception("Error al eliminar reserva: " . $e->getMessage());
        }
    }
}