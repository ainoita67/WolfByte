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
                            r.id_recurso,
                            r.descripcion,
                            r.tipo,
                            r.activo,
                            r.especial,
                            r.numero_planta,
                            e.nombre_edificio,
                            es.es_aula
                        FROM Recurso r
                        LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
                        LEFT JOIN Espacio es ON r.id_recurso = es.id_espacio
                        WHERE r.tipo = 'Espacio'
                        ORDER BY r.id_recurso;")
            ->fetchAll();
    }

    // Obtener Espacio por ID
    public function findById(string $id): ?array
    {
        $result = $this->db
            ->query("SELECT 
                            r.id_recurso,
                            r.descripcion,
                            r.tipo,
                            r.activo,
                            r.especial,
                            r.numero_planta,
                            e.nombre_edificio,
                            es.es_aula
                        FROM Recurso r
                        LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
                        LEFT JOIN Espacio es ON r.id_recurso = es.id_espacio
                        WHERE r.tipo = 'Espacio' AND r.id_recurso=:id
                        ORDER BY r.id_recurso;")
            ->bind(':id', $id)
            ->fetch();

        return $result ?: null;
    }

    // Crear Espacio
    public function create(array $data): int|false
    {
        try {
            $this->db->beginTransaction();

            // 1. Verificar que el ID no existe
            if ($this->existeRecurso($data['id_recurso'])) {
                throw new PDOException("El ID del recurso ya existe");
            }

            // 2. Verificar que el edificio existe
            if (!$this->existeEdificio($data['id_edificio'])) {
                throw new PDOException("El edificio especificado no existe");
            }

            // 3. Verificar/Crear planta
            $this->verificarOCrearPlanta($data['numero_planta'], $data['id_edificio']);

            // 4. Insertar en Recurso
            $this->db
                ->query("INSERT INTO Recurso (id_recurso, descripcion, tipo, activo, especial, numero_planta, id_edificio)
                        VALUES (:id_recurso, :descripcion, 'Espacio', :activo, :especial, :numero_planta, :id_edificio)")
                ->bind(':id_recurso', $data['id_recurso'])
                ->bind(':descripcion', $data['descripcion'])
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->bind(':numero_planta', $data['numero_planta'])
                ->bind(':id_edificio', $data['id_edificio'])
                ->execute();

            // 5. Insertar en Espacio
            $this->db
                ->query("INSERT INTO Espacio (id_espacio, es_aula)
                        VALUES (:id_espacio, :es_aula)")
                ->bind(':id_espacio', $data['id_recurso'])
                ->bind(':es_aula', $data['es_aula'])
                ->execute();

            // 6. Insertar características si existen
            if (isset($data['caracteristicas']) && is_array($data['caracteristicas'])) {
                $this->insertarCaracteristicas($data['id_recurso'], $data['caracteristicas']);
            }

            $this->db->commit();
            return 1; // Retorna 1 para indicar éxito

        } catch (PDOException $e) {
            $this->db->rollback();
            error_log("Error al crear espacio: " . $e->getMessage());
            return false;
        }
    }

    // Métodos auxiliares privados
    private function existeRecurso(string $idRecurso): bool
    {
        $result = $this->db
            ->query("SELECT COUNT(*) as count FROM Recurso WHERE id_recurso = :id_recurso")
            ->bind(':id_recurso', $idRecurso)
            ->fetch();

        return $result['count'] > 0;
    }

    private function existeEdificio(int $idEdificio): bool
    {
        $result = $this->db
            ->query("SELECT COUNT(*) as count FROM Edificio WHERE id_edificio = :id_edificio")
            ->bind(':id_edificio', $idEdificio)
            ->fetch();

        return $result['count'] > 0;
    }

    private function verificarOCrearPlanta(int $numeroPlanta, int $idEdificio): void
    {
        $result = $this->db
            ->query("SELECT COUNT(*) as count FROM Planta WHERE numero_planta = :numero_planta AND id_edificio = :id_edificio")
            ->bind(':numero_planta', $numeroPlanta)
            ->bind(':id_edificio', $idEdificio)
            ->fetch();

        if ($result['count'] == 0) {
            // Crear la planta
            $this->db
                ->query("INSERT INTO Planta (numero_planta, id_edificio) VALUES (:numero_planta, :id_edificio)")
                ->bind(':numero_planta', $numeroPlanta)
                ->bind(':id_edificio', $idEdificio)
                ->execute();
        }
    }

    private function insertarCaracteristicas(string $idEspacio, array $caracteristicas): void
    {
        $sql = "INSERT INTO Caracteristica_Espacio (id_espacio, id_caracteristica) 
                VALUES (:id_espacio, :id_caracteristica)";

        foreach ($caracteristicas as $idCaracteristica) {
            // Verificar que la característica existe
            $result = $this->db
                ->query("SELECT COUNT(*) as count FROM Caracteristica WHERE id_caracteristica = :id_caracteristica")
                ->bind(':id_caracteristica', $idCaracteristica)
                ->fetch();

            if ($result['count'] > 0) {
                $this->db
                    ->query($sql)
                    ->bind(':id_espacio', $idEspacio)
                    ->bind(':id_caracteristica', $idCaracteristica)
                    ->execute();
            }
        }
    }

    // Actualizar Espacio
    public function update(string $id, array $data): int|false
    {
        try {
            $this->db->beginTransaction();

            // 1. Actualizar Recurso
            $this->db
                ->query("UPDATE Recurso 
                        SET descripcion = :descripcion, 
                            activo = :activo, 
                            especial = :especial,
                            numero_planta = :numero_planta,
                            id_edificio = :id_edificio
                        WHERE id_recurso = :id_recurso AND tipo = 'Espacio'")
                ->bind(':id_recurso', $id)
                ->bind(':descripcion', $data['descripcion'])
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->bind(':numero_planta', $data['numero_planta'])
                ->bind(':id_edificio', $data['id_edificio'])
                ->execute();

            // 2. Actualizar Espacio
            $this->db
                ->query("UPDATE Espacio 
                        SET es_aula = :es_aula
                        WHERE id_espacio = :id_espacio")
                ->bind(':id_espacio', $id)
                ->bind(':es_aula', $data['es_aula'])
                ->execute();

            // 3. Actualizar características (eliminar existentes e insertar nuevas)
            if (isset($data['caracteristicas'])) {
                // Eliminar características actuales
                $this->db
                    ->query("DELETE FROM Caracteristica_Espacio WHERE id_espacio = :id_espacio")
                    ->bind(':id_espacio', $id)
                    ->execute();

                // Insertar nuevas características
                if (is_array($data['caracteristicas']) && !empty($data['caracteristicas'])) {
                    $this->insertarCaracteristicas($id, $data['caracteristicas']);
                }
            }

            $this->db->commit();
            return $this->db->rowCount();

        } catch (PDOException $e) {
            $this->db->rollback();
            error_log("Error al actualizar espacio: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar Espacio
    public function delete(string $id): int|false
    {
        try {
            $this->db->beginTransaction();

            // 1. Eliminar características asociadas
            $this->db
                ->query("DELETE FROM Caracteristica_Espacio WHERE id_espacio = :id_espacio")
                ->bind(':id_espacio', $id)
                ->execute();

            // 2. Eliminar de Espacio
            $this->db
                ->query("DELETE FROM Espacio WHERE id_espacio = :id_espacio")
                ->bind(':id_espacio', $id)
                ->execute();

            // 3. Eliminar de Recurso
            $this->db
                ->query("DELETE FROM Recurso WHERE id_recurso = :id_recurso AND tipo = 'Espacio'")
                ->bind(':id_recurso', $id)
                ->execute();

            $this->db->commit();
            return $this->db->rowCount();

        } catch (PDOException $e) {
            $this->db->rollback();
            error_log("Error al eliminar espacio: " . $e->getMessage());
            return false;
        }
    }

    // Obtener espacios por edificio
    public function findByEdificio(int $idEdificio): array
    {
        return $this->db
            ->query("SELECT 
                            r.id_recurso,
                            r.descripcion,
                            r.tipo,
                            r.activo,
                            r.especial,
                            r.numero_planta,
                            e.nombre_edificio,
                            es.es_aula
                        FROM Recurso r
                        LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
                        LEFT JOIN Espacio es ON r.id_recurso = es.id_espacio
                        WHERE r.tipo = 'Espacio' AND r.id_edificio = :id_edificio
                        ORDER BY r.numero_planta, r.id_recurso")
            ->bind(':id_edificio', $idEdificio)
            ->fetchAll();
    }

    // Verificar si un espacio está disponible en un rango de fechas
    public function estaDisponible(string $idEspacio, string $inicio, string $fin): bool
    {
        $result = $this->db
            ->query("SELECT COUNT(*) as count 
                    FROM Reserva_espacio re
                    JOIN Reserva r ON re.id_reserva_espacio = r.id_reserva
                    WHERE re.id_espacio = :id_espacio
                    AND r.autorizada = 1
                    AND (
                        (r.inicio <= :fin AND r.fin >= :inicio)
                        OR (r.inicio BETWEEN :inicio AND :fin)
                        OR (r.fin BETWEEN :inicio AND :fin)
                    )")
            ->bind(':id_espacio', $idEspacio)
            ->bind(':inicio', $inicio)
            ->bind(':fin', $fin)
            ->fetch();

        return $result['count'] == 0;
    }

    // En EspacioModel.php, añade estos métodos:

    // Verificar si un espacio tiene reservas activas
    public function tieneReservasActivas(string $idEspacio): bool
    {
        $result = $this->db
            ->query("SELECT COUNT(*) as count 
                FROM Reserva_espacio re
                JOIN Reserva r ON re.id_reserva_espacio = r.id_reserva
                WHERE re.id_espacio = :id_espacio
                AND r.autorizada = 1
                AND r.fin >= NOW()")
            ->bind(':id_espacio', $idEspacio)
            ->fetch();

        return $result['count'] > 0;
    }

    // Obtener características de un espacio
    public function getCaracteristicas(string $idEspacio): array
    {
        return $this->db
            ->query("SELECT c.id_caracteristica, c.nombre
                FROM Caracteristica_Espacio ce
                JOIN Caracteristica c ON ce.id_caracteristica = c.id_caracteristica
                WHERE ce.id_espacio = :id_espacio")
            ->bind(':id_espacio', $idEspacio)
            ->fetchAll();
    }
}