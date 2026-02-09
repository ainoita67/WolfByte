<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class MaterialModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todos los materiales
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        r.id_recurso,
                        r.descripcion,
                        r.tipo,
                        r.activo,
                        r.especial,
                        m.unidades
                    FROM Recurso r
                    INNER JOIN Material m ON r.id_recurso = m.id_material
                    WHERE r.tipo = 'Material'
                    ORDER BY r.id_recurso
                ")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener materiales: " . $e->getMessage());
        }
    }

    /**
     * Obtener material por ID
     */
    public function findById(string $id): array|false
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        r.id_recurso,
                        r.descripcion,
                        r.tipo,
                        r.activo,
                        r.especial,
                        m.unidades
                    FROM Recurso r
                    INNER JOIN Material m ON r.id_recurso = m.id_material
                    WHERE r.id_recurso = :id AND r.tipo = 'Material'
                ")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar el material: " . $e->getMessage());
        }
    }

    /**
     * Crear material
     */
    public function create(array $data): array
    {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // 1. Insertar en Recurso
            $this->db
                ->query("
                    INSERT INTO Recurso (
                        id_recurso, 
                        descripcion, 
                        tipo, 
                        activo, 
                        especial
                    ) VALUES (
                        :id_recurso,
                        :descripcion,
                        'Material',
                        :activo,
                        :especial
                    )
                ")
                ->bind(':id_recurso', $data['id_recurso'])
                ->bind(':descripcion', $data['descripcion'] ?? '')
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->execute();

            // 2. Insertar en Material
            $this->db
                ->query("
                    INSERT INTO Material (
                        id_material,
                        unidades
                    ) VALUES (
                        :id_material,
                        :unidades
                    )
                ")
                ->bind(':id_material', $data['id_recurso'])
                ->bind(':unidades', $data['unidades'])
                ->execute();

            $this->db->commit();

            return $this->findById($data['id_recurso']);
        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al crear el material: " . $e->getMessage());
        }
    }

    /**
     * Actualizar material
     */
    public function update(string $id, array $data): array
    {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // 1. Actualizar Recurso
            $this->db
                ->query("
                    UPDATE Recurso SET
                        descripcion = :descripcion,
                        activo = :activo,
                        especial = :especial
                    WHERE id_recurso = :id AND tipo = 'Material'
                ")
                ->bind(':id', $id)
                ->bind(':descripcion', $data['descripcion'] ?? '')
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->execute();

            // 2. Actualizar Material (si viene unidades)
            if (isset($data['unidades'])) {
                $this->db
                    ->query("
                        UPDATE Material SET
                            unidades = :unidades
                        WHERE id_material = :id
                    ")
                    ->bind(':id', $id)
                    ->bind(':unidades', $data['unidades'])
                    ->execute();
            }

            $this->db->commit();

            return $this->findById($id);
        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al actualizar el material: " . $e->getMessage());
        }
    }

    /**
     * Eliminar material
     */
    public function delete(string $id): void
    {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // Verificar si hay reservas asociadas
            $reservasCount = $this->db
                ->query("
                    SELECT COUNT(*) as count 
                    FROM Reserva_Portatiles 
                    WHERE id_material = :id
                ")
                ->bind(':id', $id)
                ->fetch();

            if ($reservasCount && $reservasCount['count'] > 0) {
                throw new \Exception("No se puede eliminar el material porque tiene reservas asociadas");
            }

            // 1. Eliminar de Material
            $this->db
                ->query("DELETE FROM Material WHERE id_material = :id")
                ->bind(':id', $id)
                ->execute();

            // 2. Eliminar de Recurso
            $this->db
                ->query("DELETE FROM Recurso WHERE id_recurso = :id AND tipo = 'Material'")
                ->bind(':id', $id)
                ->execute();

            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
            throw new \Exception("Error al eliminar el material: " . $e->getMessage());
        }
    }

    /**
     * Verificar disponibilidad del material en una fecha específica
     */
    public function checkAvailability(string $id, string $fecha): array
    {
        try {
            // Convertir fecha a inicio y fin del día
            $fechaInicio = date('Y-m-d 00:00:00', strtotime($fecha));
            $fechaFin = date('Y-m-d 23:59:59', strtotime($fecha));

            // Obtener unidades totales
            $material = $this->findById($id);
            
            if (!$material) {
                throw new \Exception('Material no encontrado');
            }

            $unidadesTotales = $material['unidades'];

            // Obtener unidades reservadas para esa fecha
            $result = $this->db
                ->query("
                    SELECT COALESCE(SUM(rp.unidades), 0) as unidades_reservadas
                    FROM Reserva_Portatiles rp
                    JOIN Reserva r ON rp.id_reserva_material = r.id_reserva
                    WHERE rp.id_material = :id
                    AND r.autorizada = 1
                    AND r.inicio <= :fecha_fin
                    AND r.fin >= :fecha_inicio
                ")
                ->bind(':id', $id)
                ->bind(':fecha_inicio', $fechaInicio)
                ->bind(':fecha_fin', $fechaFin)
                ->fetch();

            $unidadesReservadas = $result['unidades_reservadas'] ?? 0;
            $unidadesDisponibles = $unidadesTotales - $unidadesReservadas;

            return [
                'id_material' => $id,
                'fecha' => $fecha,
                'unidades_totales' => $unidadesTotales,
                'unidades_reservadas' => $unidadesReservadas,
                'unidades_disponibles' => $unidadesDisponibles,
                'disponible' => $unidadesDisponibles > 0
            ];
        } catch (PDOException $e) {
            throw new \Exception("Error al verificar disponibilidad: " . $e->getMessage());
        }
    }

    /**
     * Buscar materiales con filtros
     */
    public function search(array $filters): array
    {
        try {
            $query = "
                SELECT 
                    r.id_recurso,
                    r.descripcion,
                    r.tipo,
                    r.activo,
                    r.especial,
                    m.unidades
                FROM Recurso r
                INNER JOIN Material m ON r.id_recurso = m.id_material
                WHERE r.tipo = 'Material'
            ";

            $conditions = [];
            $params = [];

            if (!empty($filters['descripcion'])) {
                $conditions[] = "r.descripcion LIKE :descripcion";
                $params[':descripcion'] = '%' . $filters['descripcion'] . '%';
            }

            if (isset($filters['activo'])) {
                $conditions[] = "r.activo = :activo";
                $params[':activo'] = $filters['activo'] ? 1 : 0;
            }

            if (isset($filters['especial'])) {
                $conditions[] = "r.especial = :especial";
                $params[':especial'] = $filters['especial'] ? 1 : 0;
            }

            if (!empty($filters['id_recurso'])) {
                $conditions[] = "r.id_recurso LIKE :id_recurso";
                $params[':id_recurso'] = '%' . $filters['id_recurso'] . '%';
            }

            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY r.id_recurso";

            $this->db->query($query);
            
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }

            return $this->db->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar materiales: " . $e->getMessage());
        }
    }

    /**
     * Actualizar stock/unidades
     */
    public function updateStock(string $id, int $unidades): bool
    {
        try {
            return $this->db
                ->query("UPDATE Material SET unidades = :unidades WHERE id_material = :id")
                ->bind(':unidades', $unidades)
                ->bind(':id', $id)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar stock: " . $e->getMessage());
        }
    }
}