<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class PlantaModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todas las plantas con su edificio
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        p.numero_planta,
                        p.nombre_planta,
                        p.id_edificio,
                        e.nombre_edificio,
                        COUNT(esp.id_espacio) as total_espacios
                    FROM Planta p
                    INNER JOIN Edificio e ON p.id_edificio = e.id_edificio
                    LEFT JOIN Recurso r ON p.numero_planta = r.numero_planta AND p.id_edificio = r.id_edificio
                    LEFT JOIN Espacio esp ON r.id_recurso = esp.id_espacio
                    GROUP BY p.numero_planta, p.nombre_planta, p.id_edificio, e.nombre_edificio
                    ORDER BY e.nombre_edificio, p.numero_planta
                ")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener plantas: " . $e->getMessage());
        }
    }

    /**
     * Obtener plantas por edificio
     */
    public function getByEdificio(int $idEdificio): array
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        p.numero_planta,
                        p.nombre_planta,
                        p.id_edificio,
                        e.nombre_edificio,
                        COUNT(esp.id_espacio) as total_espacios
                    FROM Planta p
                    INNER JOIN Edificio e ON p.id_edificio = e.id_edificio
                    LEFT JOIN Recurso r ON p.numero_planta = r.numero_planta AND p.id_edificio = r.id_edificio
                    LEFT JOIN Espacio esp ON r.id_recurso = esp.id_espacio
                    WHERE p.id_edificio = :id_edificio
                    GROUP BY p.numero_planta, p.nombre_planta, p.id_edificio, e.nombre_edificio
                    ORDER BY p.numero_planta
                ")
                ->bind(':id_edificio', $idEdificio)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener plantas del edificio: " . $e->getMessage());
        }
    }

    /**
     * Verificar si una planta existe en un edificio
     */
    public function exists(int $numeroPlanta, int $idEdificio): bool
    {
        try {
            $result = $this->db
                ->query("
                    SELECT COUNT(*) as count 
                    FROM Planta 
                    WHERE numero_planta = :numero_planta 
                    AND id_edificio = :id_edificio
                ")
                ->bind(':numero_planta', $numeroPlanta)
                ->bind(':id_edificio', $idEdificio)
                ->fetch();
            
            return $result && $result['count'] > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al verificar planta: " . $e->getMessage());
        }
    }

    /**
     * Crear una nueva planta
     */
    public function create(int $numeroPlanta, int $idEdificio, string $nombrePlanta): bool
    {
        try {
            // Verificar que el edificio existe
            $edificioExists = $this->db
                ->query("SELECT COUNT(*) as count FROM Edificio WHERE id_edificio = :id_edificio")
                ->bind(':id_edificio', $idEdificio)
                ->fetch();
            
            if (!$edificioExists || $edificioExists['count'] == 0) {
                throw new \Exception("El edificio no existe");
            }

            // Verificar que la planta no existe ya
            if ($this->exists($numeroPlanta, $idEdificio)) {
                throw new \Exception("La planta ya existe en este edificio");
            }

            // Crear la planta con nombre_planta
            return $this->db
                ->query("
                    INSERT INTO Planta (numero_planta, id_edificio, nombre_planta)
                    VALUES (:numero_planta, :id_edificio, :nombre_planta)
                ")
                ->bind(':numero_planta', $numeroPlanta)
                ->bind(':id_edificio', $idEdificio)
                ->bind(':nombre_planta', $nombrePlanta)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al crear planta: " . $e->getMessage());
        }
    }

    /**
     * Actualizar una planta
     */
    public function update(int $numeroPlantaActual, int $idEdificio, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // Si se quiere cambiar el número de planta
            if (isset($data['nuevo_numero_planta'])) {
                $nuevoNumero = (int)$data['nuevo_numero_planta'];
                
                // Verificar que el nuevo número no existe (si es diferente)
                if ($nuevoNumero !== $numeroPlantaActual && $this->exists($nuevoNumero, $idEdificio)) {
                    throw new \Exception("Ya existe una planta con ese número en este edificio");
                }

                // Actualizar todos los recursos que referencian esta planta
                $this->db
                    ->query("
                        UPDATE Recurso 
                        SET numero_planta = :nuevo_numero 
                        WHERE numero_planta = :numero_actual 
                        AND id_edificio = :id_edificio
                    ")
                    ->bind(':nuevo_numero', $nuevoNumero)
                    ->bind(':numero_actual', $numeroPlantaActual)
                    ->bind(':id_edificio', $idEdificio)
                    ->execute();

                // Actualizar el número de planta
                $this->db
                    ->query("
                        UPDATE Planta 
                        SET numero_planta = :nuevo_numero 
                        WHERE numero_planta = :numero_actual 
                        AND id_edificio = :id_edificio
                    ")
                    ->bind(':nuevo_numero', $nuevoNumero)
                    ->bind(':numero_actual', $numeroPlantaActual)
                    ->bind(':id_edificio', $idEdificio)
                    ->execute();

                $numeroPlantaActual = $nuevoNumero; // Actualizar para el siguiente paso
            }

            // Si se quiere cambiar el nombre de planta
            if (isset($data['nombre_planta'])) {
                $this->db
                    ->query("
                        UPDATE Planta 
                        SET nombre_planta = :nombre_planta
                        WHERE numero_planta = :numero_planta 
                        AND id_edificio = :id_edificio
                    ")
                    ->bind(':nombre_planta', $data['nombre_planta'])
                    ->bind(':numero_planta', $numeroPlantaActual)
                    ->bind(':id_edificio', $idEdificio)
                    ->execute();
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw new \Exception("Error al actualizar planta: " . $e->getMessage());
        }
    }

    /**
     * Eliminar una planta
     */
    public function delete(int $numeroPlanta, int $idEdificio): bool
    {
        try {
            // Verificar que no haya recursos asociados
            $recursosCount = $this->db
                ->query("
                    SELECT COUNT(*) as count 
                    FROM Recurso 
                    WHERE numero_planta = :numero_planta 
                    AND id_edificio = :id_edificio
                ")
                ->bind(':numero_planta', $numeroPlanta)
                ->bind(':id_edificio', $idEdificio)
                ->fetch();
            
            if ($recursosCount && $recursosCount['count'] > 0) {
                throw new \Exception("No se puede eliminar la planta porque tiene recursos asociados");
            }

            // Eliminar la planta
            return $this->db
                ->query("
                    DELETE FROM Planta 
                    WHERE numero_planta = :numero_planta 
                    AND id_edificio = :id_edificio
                ")
                ->bind(':numero_planta', $numeroPlanta)
                ->bind(':id_edificio', $idEdificio)
                ->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar planta: " . $e->getMessage());
        }
    }

    /**
     * Obtener detalles específicos de una planta
     */
    public function getDetails(int $numeroPlanta, int $idEdificio): array|false
    {
        try {
            return $this->db
                ->query("
                    SELECT 
                        p.numero_planta,
                        p.nombre_planta,
                        p.id_edificio,
                        e.nombre_edificio,
                        e.id_edificio,
                        (
                            SELECT COUNT(*) 
                            FROM Recurso r 
                            WHERE r.numero_planta = p.numero_planta 
                            AND r.id_edificio = p.id_edificio
                        ) as total_recursos,
                        (
                            SELECT COUNT(*) 
                            FROM Recurso r 
                            INNER JOIN Espacio esp ON r.id_recurso = esp.id_espacio
                            WHERE r.numero_planta = p.numero_planta 
                            AND r.id_edificio = p.id_edificio
                        ) as total_espacios,
                        (
                            SELECT COUNT(*) 
                            FROM Recurso r 
                            WHERE r.tipo = 'Material' 
                            AND r.numero_planta = p.numero_planta 
                            AND r.id_edificio = p.id_edificio
                        ) as total_materiales
                    FROM Planta p
                    INNER JOIN Edificio e ON p.id_edificio = e.id_edificio
                    WHERE p.numero_planta = :numero_planta 
                    AND p.id_edificio = :id_edificio
                ")
                ->bind(':numero_planta', $numeroPlanta)
                ->bind(':id_edificio', $idEdificio)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener detalles de la planta: " . $e->getMessage());
        }
    }
}