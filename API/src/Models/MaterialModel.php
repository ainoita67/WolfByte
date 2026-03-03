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
                        r.especial,
                        m.unidades,
                        p.nombre_planta,
                        e.nombre_edificio,
                        p.numero_planta,
                        e.id_edificio
                    FROM Recurso r
                    INNER JOIN Material m ON r.id_recurso = m.id_material
                    INNER JOIN Planta p ON r.numero_planta = p.numero_planta AND r.id_edificio = p.id_edificio
                    INNER JOIN Edificio e ON e.id_edificio = r.id_edificio
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

    public function getCarritosDisponibilidad($inicio, $fin, $dia_semana, $hora_inicio, $hora_fin): array
    {
        return $this->db
            ->query("SELECT 
                    r.id_recurso,
                    r.descripcion,
                    p.nombre_planta,
                    ed.nombre_edificio,
                    m.unidades,
                    -- unidades reservadas = puntuales + permanentes - liberadas (no negativo y int)
                    CAST(GREATEST(
                        COALESCE(puntuales.unidades_puntuales, 0)
                        + COALESCE(permanentes.unidades_permanentes, 0)
                        - COALESCE(liberadas.unidades_liberadas, 0),
                    0) AS UNSIGNED) AS unidades_reservadas
                FROM Recurso r
                JOIN Material m
                    ON r.id_recurso = m.id_material
                LEFT JOIN Planta p
                    ON r.numero_planta = p.numero_planta 
                    AND r.id_edificio = p.id_edificio
                LEFT JOIN Edificio ed
                    ON ed.id_edificio = r.id_edificio

                /* Reservas puntuales agregadas por material (id_material = id_recurso) */
                LEFT JOIN (
                    SELECT rp.id_material, SUM(rp.unidades) AS unidades_puntuales
                    FROM Reserva_Portatiles rp
                    JOIN Reserva res
                        ON res.id_reserva = rp.id_reserva_material
                        AND res.inicio < :res_fin
                        AND res.fin > :res_inicio
                    GROUP BY rp.id_material
                ) puntuales
                    ON puntuales.id_material = r.id_recurso

                /* Reservas permanentes agregadas por recurso */
                LEFT JOIN (
                    SELECT rp.id_recurso, SUM(rp.unidades) AS unidades_permanentes
                    FROM Reserva_permanente rp
                    WHERE rp.activo = true
                    AND rp.dia_semana = :dia_semana
                    AND rp.inicio < :rp_fin
                    AND rp.fin > :rp_inicio
                    GROUP BY rp.id_recurso
                ) permanentes
                    ON permanentes.id_recurso = r.id_recurso

                /* Liberaciones puntuales: sumadas por recurso (vía Reserva_permanente -> rp.id_recurso) */
                LEFT JOIN (
                    SELECT rp.id_recurso, COALESCE(SUM(lp.unidades), 0) AS unidades_liberadas
                    FROM Liberacion_puntual lp
                    JOIN Reserva_permanente rp
                        ON rp.id_reserva_permanente = lp.id_reserva_permanente
                        AND rp.activo = true
                        AND rp.dia_semana = :dia_semana2
                        AND rp.inicio < :rp_fin2
                        AND rp.fin > :rp_inicio2
                    WHERE lp.inicio < :lp_fin
                    AND lp.fin > :lp_inicio
                    GROUP BY rp.id_recurso
                ) liberadas
                    ON liberadas.id_recurso = r.id_recurso

                GROUP BY 
                    r.id_recurso,
                    r.descripcion,
                    p.nombre_planta,
                    ed.nombre_edificio,
                    m.unidades

                ORDER BY 
                    ed.nombre_edificio,
                    p.numero_planta,
                    r.id_recurso
            ")
            ->bind(':dia_semana', $dia_semana)
            ->bind(':rp_inicio', $hora_inicio)
            ->bind(':rp_fin', $hora_fin)
            ->bind(':res_inicio', $inicio)
            ->bind(':res_fin', $fin)
            ->bind(':lp_inicio', $inicio)
            ->bind(':lp_fin', $fin)
            ->bind(':dia_semana2', $dia_semana)
            ->bind(':rp_inicio2', $hora_inicio)
            ->bind(':rp_fin2', $hora_fin)
            ->fetchAll();
    }
}