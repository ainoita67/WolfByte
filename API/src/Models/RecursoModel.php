<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class RecursoModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todos los recursos activos
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Recurso")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener recursos");
        }
    }

    /**
     * Obtener todos los recursos activos
     */
    public function getAllActivos(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Recurso WHERE activo=1")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener recursos");
        }
    }

    /**
     * Obtener recurso por ID
     */
    public function findById(string $id): array
    {
        $result = $this->db
            ->query("SELECT r.id_recurso, r.descripcion, r.tipo, r.activo, r.especial, r.numero_planta, p.nombre_planta, r.id_edificio, ed.nombre_edificio, e.es_aula, m.unidades
                FROM Recurso r
                LEFT JOIN Espacio e ON r.id_recurso = e.id_espacio
                LEFT JOIN Material m ON m.id_material = r.id_recurso
                LEFT JOIN Planta p ON r.numero_planta = p.numero_planta AND r.id_edificio = p.id_edificio
                LEFT JOIN Edificio ed on r.id_edificio = ed.id_edificio
                WHERE r.id_recurso = :id")
            ->bind(':id', $id)
            ->fetch();

        if (!$result) {
            throw new \Exception("Recurso no encontrado");
        }

        return $result;

    }

    /**
     * Activar recurso
     */
    public function activo(string $id): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Recurso SET
                        activo = :activo
                    WHERE id_recurso = :id
                ")
                ->bind(':id',           $id)
                ->bind(':activo',       1)
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al cambiar estado de activo del recurso");
        }
    }

    /**
     * Desctivar recurso
     */
    public function desactivo(string $id): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Recurso SET
                        activo = :activo
                    WHERE id_recurso = :id
                ")
                ->bind(':id',           $id)
                ->bind(':activo',       0)
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al cambiar estado de activo del recurso");
        }
    }

}