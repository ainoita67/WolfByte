<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

class CaracteristicaEspacioModel
{
    private DB $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todas las relaciones espacio-característica
     */
    public function getAll(): array
    {
        return $this->db
            ->query("
                SELECT ce.id_espacio, ce.id_caracteristica, 
                       e.descripcion as espacio_descripcion,
                       c.nombre as caracteristica_nombre
                FROM Caracteristica_Espacio ce
                INNER JOIN Recurso e ON ce.id_espacio = e.id_recurso
                INNER JOIN Caracteristica c ON ce.id_caracteristica = c.id_caracteristica
                ORDER BY ce.id_espacio, c.nombre
            ")
            ->fetchAll();
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
     * Verificar si un espacio tiene una característica específica
     */
    public function tieneCaracteristica(string $idEspacio, int $idCaracteristica): bool
    {
        $result = $this->db
            ->query("
                SELECT COUNT(*) as total 
                FROM Caracteristica_Espacio 
                WHERE id_espacio = :id_espacio AND id_caracteristica = :id_caracteristica
            ")
            ->bind(':id_espacio', $idEspacio)
            ->bind(':id_caracteristica', $idCaracteristica)
            ->fetch();

        return $result['total'] > 0;
    }

    /**
     * Contar cuántos espacios tienen una característica
     */
    public function contarEspaciosConCaracteristica(int $idCaracteristica): int
    {
        $result = $this->db
            ->query("
                SELECT COUNT(*) as total 
                FROM Caracteristica_Espacio 
                WHERE id_caracteristica = :id_caracteristica
            ")
            ->bind(':id_caracteristica', $idCaracteristica)
            ->fetch();

        return (int)$result['total'];
    }
}