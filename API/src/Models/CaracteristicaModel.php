<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class CaracteristicaModel
{
    private DB $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todos las Caracteristicas
     */
    public function getAll(): array
    {
        return $this->db
            ->query("SELECT * FROM Caracteristica ORDER BY nombre")
            ->fetchAll();
    }

    /**
     * Obtener Caracteristica por ID
     */
    public function findById(int $id): ?array
    {
        $result = $this->db
            ->query("SELECT * FROM Caracteristica WHERE id_caracteristica = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ?: null;
    }

    /**
     * Obtener características de un espacio específico
     */
    public function getByEspacio(string $idEspacio): array
    {
        return $this->db
            ->query("
                SELECT c.* 
                FROM Caracteristica c
                INNER JOIN Caracteristica_Espacio ce ON c.id_caracteristica = ce.id_caracteristica
                WHERE ce.id_espacio = :id_espacio
                ORDER BY c.nombre
            ")
            ->bind(':id_espacio', $idEspacio)
            ->fetchAll();
    }

    /**
     * Obtener características disponibles para un espacio (las que no tiene asignadas)
     */
    public function getDisponiblesParaEspacio(string $idEspacio): array
    {
        return $this->db
            ->query("
                SELECT c.* 
                FROM Caracteristica c
                WHERE c.id_caracteristica NOT IN (
                    SELECT ce.id_caracteristica 
                    FROM Caracteristica_Espacio ce 
                    WHERE ce.id_espacio = :id_espacio
                )
                ORDER BY c.nombre
            ")
            ->bind(':id_espacio', $idEspacio)
            ->fetchAll();
    }

    /**
     * Asignar característica a un espacio
     */
    public function asignarAEspacio(string $idEspacio, int $idCaracteristica): bool
    {
        // Verificar si ya existe la asignación
        $existe = $this->db
            ->query("
                SELECT COUNT(*) as total 
                FROM Caracteristica_Espacio 
                WHERE id_espacio = :id_espacio AND id_caracteristica = :id_caracteristica
            ")
            ->bind(':id_espacio', $idEspacio)
            ->bind(':id_caracteristica', $idCaracteristica)
            ->fetch();

        if ($existe['total'] > 0) {
            return false; // Ya está asignada
        }

        $this->db
            ->query("
                INSERT INTO Caracteristica_Espacio (id_espacio, id_caracteristica)
                VALUES (:id_espacio, :id_caracteristica)
            ")
            ->bind(':id_espacio', $idEspacio)
            ->bind(':id_caracteristica', $idCaracteristica)
            ->execute();

        return true;
    }

    /**
     * Quitar característica de un espacio
     */
    public function quitarDeEspacio(string $idEspacio, int $idCaracteristica): bool
    {
        $this->db
            ->query("
                DELETE FROM Caracteristica_Espacio 
                WHERE id_espacio = :id_espacio AND id_caracteristica = :id_caracteristica
            ")
            ->bind(':id_espacio', $idEspacio)
            ->bind(':id_caracteristica', $idCaracteristica)
            ->execute();

        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'] > 0;
    }

    /**
     * Crear Caracteristica
     */
    public function create(array $data): int|false
    {
        // Verificar si ya existe una característica con el mismo nombre
        $existe = $this->db
            ->query("SELECT COUNT(*) as total FROM Caracteristica WHERE nombre = :nombre")
            ->bind(':nombre', $data['nombre'])
            ->fetch();

        if ($existe['total'] > 0) {
            return false; // Ya existe
        }

        $this->db
            ->query("
                INSERT INTO Caracteristica (nombre)
                VALUES (:nombre)
            ")
            ->bind(':nombre', $data['nombre'])
            ->execute();

        return (int) $this->db->lastId();
    }

    /**
     * Actualizar Caracteristica
     */
    public function update(int $id, array $data): int
    {
        // Verificar si ya existe otra característica con el mismo nombre
        $existe = $this->db
            ->query("
                SELECT COUNT(*) as total 
                FROM Caracteristica 
                WHERE nombre = :nombre AND id_caracteristica != :id
            ")
            ->bind(':nombre', $data['nombre'])
            ->bind(':id', $id)
            ->fetch();

        if ($existe['total'] > 0) {
            return -1; // Conflicto: ya existe otro con ese nombre
        }

        $this->db
            ->query("
                UPDATE Caracteristica
                SET nombre = :nombre
                WHERE id_caracteristica = :id
            ")
            ->bind(':nombre', $data['nombre'])
            ->bind(':id', $id)
            ->execute();

        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'];
    }

    /**
     * Eliminar Caracteristica
     */
    public function delete(int $id): int
    {
        // Verificar si la característica está siendo usada
        $enUso = $this->db
            ->query("
                SELECT COUNT(*) as total 
                FROM Caracteristica_Espacio 
                WHERE id_caracteristica = :id
            ")
            ->bind(':id', $id)
            ->fetch();

        if ($enUso['total'] > 0) {
            return -1; // Está en uso
        }

        $this->db
            ->query("DELETE FROM Caracteristica WHERE id_caracteristica = :id")
            ->bind(':id', $id)
            ->execute();

        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'];
    }

    /**
     * Buscar características por nombre (para autocompletado)
     */
    public function search(string $termino): array
    {
        return $this->db
            ->query("
                SELECT * FROM Caracteristica 
                WHERE nombre LIKE :termino 
                ORDER BY nombre
                LIMIT 10
            ")
            ->bind(':termino', "%$termino%")
            ->fetchAll();
    }
}