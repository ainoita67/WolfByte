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
        try {
            $result = $this->db
                ->query("SELECT * FROM Recurso WHERE id_recurso = :id")
                ->bind(':id', $id)
                ->fetch();

            if (!$result) {
                throw new \Exception("Recurso no encontrado");
            }

            return $result;
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener recurso por ID");
        }
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