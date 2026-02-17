<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class EspacioModel
{
    private DB $db;
    public function __construct()
    {
        $this->db = new DB();
    }

    // Obtener todos los Espacios
    public function getAll(): array
    {
        return $this->db
            ->query("SELECT 
                        e.id_espacio,
                        e.es_aula,
                        r.descripcion,
                        r.activo,
                        r.especial,
                        r.numero_planta,
                        r.id_edificio,
                        ed.nombre_edificio,
                        GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.nombre ASC SEPARATOR ', ') as caracteristicas
                    FROM Espacio e
                    INNER JOIN Recurso r ON e.id_espacio = r.id_recurso
                    LEFT JOIN Edificio ed ON r.id_edificio = ed.id_edificio
                    LEFT JOIN Caracteristica_Espacio ce ON e.id_espacio = ce.id_espacio
                    LEFT JOIN Caracteristica c ON ce.id_caracteristica = c.id_caracteristica
                    WHERE r.tipo = 'Espacio'
                    GROUP BY e.id_espacio, e.es_aula, r.descripcion, r.activo, r.especial, 
                            r.numero_planta, r.id_edificio, ed.nombre_edificio
                    ORDER BY ed.nombre_edificio, r.numero_planta, e.id_espacio")
            ->fetchAll();
    }

    /**
     * Obtener espacio por ID
     */
    public function findById(string $id): ?array
    {
        $result = $this->db
            ->query("SELECT 
                    e.id_espacio,
                    e.es_aula,
                    r.descripcion,
                    r.activo,
                    r.especial,
                    r.numero_planta,
                    r.id_edificio,
                    ed.nombre_edificio,
                    GROUP_CONCAT(DISTINCT c.id_caracteristica) as caracteristicas_ids,
                    GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.nombre ASC SEPARATOR ', ') as caracteristicas_nombres
                FROM Espacio e
                INNER JOIN Recurso r ON e.id_espacio = r.id_recurso
                LEFT JOIN Edificio ed ON r.id_edificio = ed.id_edificio
                LEFT JOIN Caracteristica_Espacio ce ON e.id_espacio = ce.id_espacio
                LEFT JOIN Caracteristica c ON ce.id_caracteristica = c.id_caracteristica
                WHERE e.id_espacio = :id AND r.tipo = 'Espacio'
                GROUP BY e.id_espacio, e.es_aula, r.descripcion, r.activo, r.especial, 
                         r.numero_planta, r.id_edificio, ed.nombre_edificio
            ")
            ->bind(':id', $id)
            ->fetch();

        return $result ?: null;
    }

    /**
     * Crear un nuevo espacio
     */
    public function create(array $data): string|false
    {   
        $this->db->beginTransaction();
        
        try {

            // Insertar espacio en Recurso
            $this->db
                ->query("INSERT INTO Recurso (id_recurso, descripcion, tipo, activo, especial, numero_planta, id_edificio)
                    VALUES (:id_recurso, :descripcion, 'Espacio', :activo, :especial, :numero_planta, :id_edificio)
                ")
                ->bind(':id_recurso', $data['id_espacio'])
                ->bind(':descripcion', $data['descripcion'] ?? null)
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->bind(':numero_planta', $data['numero_planta'] ?? null)
                ->bind(':id_edificio', $data['id_edificio'] ?? null)
                ->execute();

            // Insertar en Espacio
            $this->db
                ->query("INSERT INTO Espacio (id_espacio, es_aula)
                    VALUES (:id_espacio, :es_aula)
                ")
                ->bind(':id_espacio', $data['id_espacio'])
                ->bind(':es_aula', $data['es_aula'] ?? 0)
                ->execute();

            // 5. Insertar características si existen
            if (!empty($data['caracteristicasId']) && is_array($data['caracteristicasId'])) {
                $this->addCaracteristicas($data['id_espacio'], $data['caracteristicasId']);
            }

            $this->db->commit();
            return $data['id_espacio'];

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error al crear espacio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar espacio
     */
    public function update(string $id, array $data): int
    {
        $this->db->beginTransaction();
        
        try {
            // 1. Actualizar Recurso
            $this->db
                ->query("UPDATE Recurso 
                    SET descripcion = :descripcion,
                        activo = :activo,
                        especial = :especial,
                        numero_planta = :numero_planta,
                        id_edificio = :id_edificio
                    WHERE id_recurso = :id AND tipo = 'Espacio'
                ")
                ->bind(':descripcion', $data['descripcion'] ?? null)
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->bind(':numero_planta', $data['numero_planta'] ?? null)
                ->bind(':id_edificio', $data['id_edificio'] ?? null)
                ->bind(':id', $id)
                ->execute();

            // 2. Actualizar Espacio
            $this->db
                ->query("UPDATE Espacio 
                    SET es_aula = :es_aula
                    WHERE id_espacio = :id
                ")
                ->bind(':es_aula', $data['es_aula'] ?? 0)
                ->bind(':id', $id)
                ->execute();

            // 3. Actualizar edificio si se proporciona nombre
            if (!empty($data['nombre_edificio'])) {
                $edificioId = $this->getOrCreateEdificio($data['nombre_edificio']);
                $this->db
                    ->query("UPDATE Recurso SET id_edificio = :id_edificio WHERE id_recurso = :id")
                    ->bind(':id_edificio', $edificioId)
                    ->bind(':id', $id)
                    ->execute();
            }

            // 4. Actualizar características
            if (isset($data['caracteristicas'])) {
                // Eliminar características existentes
                $this->db
                    ->query("DELETE FROM Caracteristica_Espacio WHERE id_espacio = :id")
                    ->bind(':id', $id)
                    ->execute();
                
                // Agregar nuevas características
                if (is_array($data['caracteristicas']) && !empty($data['caracteristicas'])) {
                    $this->addCaracteristicas($id, $data['caracteristicas']);
                }
            }

            $this->db->commit();
            
            return $this->db
                ->query("SELECT ROW_COUNT() AS affected")
                ->fetch()['affected'];

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error al actualizar espacio: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Eliminar espacio
     */
    public function delete(string $id): int
    {
        $this->db->beginTransaction();
        
        try {
            // 1. Eliminar características asociadas
            $this->db
                ->query("DELETE FROM Caracteristica_Espacio WHERE id_espacio = :id")
                ->bind(':id', $id)
                ->execute();

            // 2. Eliminar el espacio
            $this->db
                ->query("DELETE FROM Espacio WHERE id_espacio = :id")
                ->bind(':id', $id)
                ->execute();

            // 3. Eliminar el recurso
            $this->db
                ->query("DELETE FROM Recurso WHERE id_recurso = :id AND tipo = 'Espacio'")
                ->bind(':id', $id)
                ->execute();

            $this->db->commit();
            
            return $this->db
                ->query("SELECT ROW_COUNT() AS affected")
                ->fetch()['affected'];

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error al eliminar espacio: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener espacios por edificio
     */
    public function getByEdificio(int $idEdificio): array
    {
        return $this->db
            ->query("SELECT 
                    e.id_espacio,
                    e.es_aula,
                    r.descripcion,
                    r.activo,
                    r.especial,
                    r.numero_planta,
                    ed.nombre_edificio
                FROM Espacio e
                INNER JOIN Recurso r ON e.id_espacio = r.id_recurso
                LEFT JOIN Edificio ed ON r.id_edificio = ed.id_edificio
                WHERE r.id_edificio = :id_edificio AND r.tipo = 'Espacio'
                ORDER BY r.numero_planta, e.id_espacio
            ")
            ->bind(':id_edificio', $idEdificio)
            ->fetchAll();
    }

    /**
     * Obtener espacios activos
     */
    public function getActivos(): array
    {
        return $this->db
            ->query("SELECT
                    e.id_espacio,
                    e.es_aula,
                    r.descripcion,
                    r.activo,
                    r.especial,
                    r.numero_planta,
                    ed.nombre_edificio
                FROM Espacio e
                INNER JOIN Recurso r ON e.id_espacio = r.id_recurso
                LEFT JOIN Edificio ed ON r.id_edificio = ed.id_edificio
                WHERE r.activo = 1 AND r.tipo = 'Espacio'
                ORDER BY ed.nombre_edificio, r.numero_planta, e.id_espacio
            ")
            ->fetchAll();
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleEstado(string $id, int $estado): bool
    {
        return $this->db
            ->query("UPDATE Recurso SET activo = :estado WHERE id_recurso = :id AND tipo = 'Espacio'")
            ->bind(':estado', $estado)
            ->bind(':id', $id)
            ->execute();
    }

    /**
     * Métodos auxiliares
     */

    /**
     * Obtener o crear edificio
     */
    private function getOrCreateEdificio(string $nombreEdificio): ?int
    {
        // Buscar edificio existente
        $edificio = $this->db
            ->query("SELECT id_edificio FROM Edificio WHERE nombre_edificio = :nombre")
            ->bind(':nombre', $nombreEdificio)
            ->fetch();

        if ($edificio) {
            return (int) $edificio['id_edificio'];
        }

        // Crear nuevo edificio
        $this->db
            ->query("INSERT INTO Edificio (nombre_edificio) VALUES (:nombre)")
            ->bind(':nombre', $nombreEdificio)
            ->execute();

        return (int) $this->db->lastId();
    }

    /**
     * Agregar características a un espacio
     */
    public function addCaracteristicas(string $idEspacio, array $caracteristicasId): void
    {
        var_dump($idEspacio);
        foreach ($caracteristicasId as $caracteristicaId) {
            $this->db
                ->query("INSERT INTO `Caracteristica_Espacio`(`id_espacio`, `id_caracteristica`) VALUES (:id_espacio, :id_caracteristica)")
                ->bind(':id_espacio', $idEspacio)
                ->bind(':id_caracteristica', (int) $caracteristicaId)
                ->execute();
        }
    }

    /**
     * Obtener características de un espacio
     */
    public function getCaracteristicas(string $idEspacio): array
    {
        return $this->db
            ->query("SELECT c.id_caracteristica, c.nombre
                FROM Caracteristica c
                INNER JOIN Caracteristica_Espacio ce ON c.id_caracteristica = ce.id_caracteristica
                WHERE ce.id_espacio = :id_espacio
                ORDER BY c.nombre
            ")
            ->bind(':id_espacio', $idEspacio)
            ->fetchAll();
    }

    /**
     * Buscar espacios por características
     */
    // public function searchByCaracteristicas(array $caracteristicasIds): array
    // {
    //     $placeholders = implode(',', array_fill(0, count($caracteristicasIds), '?'));
        
    //     return $this->db
    //         ->query("
    //             SELECT 
    //                 e.id_espacio,
    //                 e.es_aula,
    //                 r.descripcion,
    //                 r.activo,
    //                 r.especial,
    //                 r.numero_planta,
    //                 ed.nombre_edificio,
    //                 COUNT(DISTINCT ce.id_caracteristica) as caracteristicas_count
    //             FROM Espacio e
    //             INNER JOIN Recurso r ON e.id_espacio = r.id_recurso
    //             LEFT JOIN Edificio ed ON r.id_edificio = ed.id_edificio
    //             INNER JOIN Caracteristica_Espacio ce ON e.id_espacio = ce.id_espacio
    //             WHERE r.tipo = 'Espacio' AND r.activo = 1 
    //                   AND ce.id_caracteristica IN ($placeholders)
    //             GROUP BY e.id_espacio, e.es_aula, r.descripcion, r.activo, r.especial, 
    //                      r.numero_planta, ed.nombre_edificio
    //             HAVING caracteristicas_count = ?
    //             ORDER BY ed.nombre_edificio, r.numero_planta
    //         ")
    //         ->bindMultiple(array_merge($caracteristicasIds, [count($caracteristicasIds)]))
    //         ->fetchAll();
    // }
}