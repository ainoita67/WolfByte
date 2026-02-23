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
        $totalAffected = 0;

        try {
            // 1. Verificar/crear planta si es necesario
            if (!empty($data['numero_planta']) && !empty($data['id_edificio'])) {
                $existePlanta = $this->db
                    ->query("SELECT 1 FROM Planta WHERE numero_planta = :numero_planta AND id_edificio = :id_edificio")
                    ->bind(':numero_planta', $data['numero_planta'])
                    ->bind(':id_edificio', $data['id_edificio'])
                    ->fetch();

                if (!$existePlanta) {
                    $this->db
                        ->query("INSERT INTO Planta (numero_planta, id_edificio) VALUES (:numero_planta, :id_edificio)")
                        ->bind(':numero_planta', $data['numero_planta'])
                        ->bind(':id_edificio', $data['id_edificio'])
                        ->execute();
                    // No sumamos al contador porque es una inserción, no una actualización del espacio
                }
            }

            // 2. Si se proporciona nombre_edificio, obtener o crear el edificio
            if (!empty($data['nombre_edificio'])) {
                $edificioId = $this->getOrCreateEdificio($data['nombre_edificio']);
                $data['id_edificio'] = $edificioId;
                // No sumamos al contador porque es una operación auxiliar
            }

            // 3. Actualizar Recurso
            $recursoUpdates = [];
            $recursoParams = [':id' => $id];

            $fields = [];
            if (array_key_exists('descripcion', $data)) {
                $fields[] = "descripcion = :descripcion";
                $recursoParams[':descripcion'] = $data['descripcion'];
            }
            if (array_key_exists('activo', $data)) {
                $fields[] = "activo = :activo";
                $recursoParams[':activo'] = $data['activo'];
            }
            if (array_key_exists('especial', $data)) {
                $fields[] = "especial = :especial";
                $recursoParams[':especial'] = $data['especial'];
            }
            if (array_key_exists('numero_planta', $data)) {
                $fields[] = "numero_planta = :numero_planta";
                $recursoParams[':numero_planta'] = $data['numero_planta'];
            }
            if (array_key_exists('id_edificio', $data)) {
                $fields[] = "id_edificio = :id_edificio";
                $recursoParams[':id_edificio'] = $data['id_edificio'];
            }

            if (!empty($fields)) {
                $recursoQuery = "UPDATE Recurso SET " . implode(', ', $fields) . " WHERE id_recurso = :id AND tipo = 'Espacio'";

                $stmt = $this->db->query($recursoQuery);
                foreach ($recursoParams as $param => $value) {
                    $stmt->bind($param, $value);
                }
                $stmt->execute();

                // Verificar si realmente se actualizó algo
                $recursoAffected = $this->db
                    ->query("SELECT ROW_COUNT() AS affected")
                    ->fetch()['affected'];
                $totalAffected += (int) $recursoAffected;
            }

            // 4. Actualizar Espacio (solo si se proporciona es_aula)
            if (array_key_exists('es_aula', $data)) {
                $this->db
                    ->query("UPDATE Espacio SET es_aula = :es_aula WHERE id_espacio = :id")
                    ->bind(':es_aula', $data['es_aula'])
                    ->bind(':id', $id)
                    ->execute();

                $espacioAffected = $this->db
                    ->query("SELECT ROW_COUNT() AS affected")
                    ->fetch()['affected'];
                $totalAffected += (int) $espacioAffected;
            }

            // 5. Actualizar características si se proporcionan
            if (array_key_exists('caracteristicasId', $data)) {
                // Log para depuración
                error_log("Actualizando características. Datos recibidos: " . json_encode($data['caracteristicasId']));

                // Eliminar características existentes
                $deleteResult = $this->db
                    ->query("DELETE FROM Caracteristica_Espacio WHERE id_espacio = :id")
                    ->bind(':id', $id)
                    ->execute();

                error_log("Eliminación de características: " . ($deleteResult ? 'éxito' : 'fallo'));

                // Agregar nuevas características SOLO si el array no está vacío
                if (!empty($data['caracteristicasId']) && is_array($data['caracteristicasId'])) {
                    $this->addCaracteristicas($id, $data['caracteristicasId']);
                } else {
                    error_log("No hay características para insertar o el array está vacío");
                }

                // Consideramos que hubo cambios en características
                $totalAffected += 1;
            }

            $this->db->commit();

            // Si totalAffected > 0, hubo cambios reales
            return $totalAffected > 0 ? $totalAffected : 0;

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error al actualizar espacio: " . $e->getMessage());
            throw $e;
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

    private function addCaracteristicas(string $idEspacio, array $caracteristicasId): void
    {
        foreach ($caracteristicasId as $caracteristicaId) {
            // Verificar que la característica existe antes de insertar
            $existe = $this->db
                ->query("SELECT 1 FROM Caracteristica WHERE id_caracteristica = :id")
                ->bind(':id', $caracteristicaId)
                ->fetch();

            if ($existe) {
                $this->db
                    ->query("INSERT INTO Caracteristica_Espacio (id_espacio, id_caracteristica) VALUES (:id_espacio, :id_caracteristica)")
                    ->bind(':id_espacio', $idEspacio)
                    ->bind(':id_caracteristica', (int) $caracteristicaId)
                    ->execute();
            } else {
                error_log("Característica con ID $caracteristicaId no existe, se omite");
            }
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
     * Obtener espacios libres entre dos fechas
     */
    public function getEspaciosLibres(string $fechaInicio, string $fechaFin): array
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
                    p.nombre_planta,
                    GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.nombre ASC SEPARATOR ', ') as caracteristicas
                FROM Espacio e
                INNER JOIN Recurso r ON e.id_espacio = r.id_recurso
                LEFT JOIN Edificio ed ON r.id_edificio = ed.id_edificio
                LEFT JOIN Planta p ON r.numero_planta = p.numero_planta AND r.id_edificio = p.id_edificio
                LEFT JOIN Caracteristica_Espacio ce ON e.id_espacio = ce.id_espacio
                LEFT JOIN Caracteristica c ON ce.id_caracteristica = c.id_caracteristica
                WHERE r.tipo = 'Espacio'
                AND r.activo = 1
                AND e.id_espacio NOT IN (
                    SELECT DISTINCT re.id_espacio
                    FROM Reserva_espacio re
                    INNER JOIN Reserva res ON re.id_reserva = res.id_reserva
                    WHERE res.autorizada = 1
                    AND (
                        (res.inicio < :fecha_fin AND res.fin > :fecha_inicio)
                    )
                )
                GROUP BY e.id_espacio, e.es_aula, r.descripcion, r.activo, r.especial, 
                        r.numero_planta, r.id_edificio, ed.nombre_edificio, p.nombre_planta
                ORDER BY ed.nombre_edificio, r.numero_planta, e.id_espacio")
            ->bind(':fecha_inicio', $fechaInicio)
            ->bind(':fecha_fin', $fechaFin)
            ->fetchAll();
    }

}