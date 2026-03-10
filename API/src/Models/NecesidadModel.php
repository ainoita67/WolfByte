<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class NecesidadModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todas las necesidades
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Necesidad")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener necesidades");
        }
    }

    /**
     * Obtener necesidad por nombre
     */
    public function findByNombre(string $nombre): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Necesidad WHERE lower(nombre) = lower(:nombre)")
                ->bind(':nombre', $nombre)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la necesidad");
        }
    }

    /**
     * Crear necesidad
     */
    public function create(array $data): array
    {
        try {
            if($this->findByNombre($data['nombre'])) {
                throw new \Exception("La necesidad con ese nombre ya existe");
            }else{
                $this->db
                    ->query("
                        INSERT INTO Necesidad (nombre)
                        VALUES (:necesidad)
                    ")
                    ->bind(':necesidad', $data['nombre'])
                    ->execute();

                return $this->findByNombre($data['nombre']);
            }
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la necesidad");
        }
    }

    /**
     * Actualizar necesidad
     */
    public function update(int $id, string $nombre): array
    {
        try {
            if($this->findByNombre($nombre)) {
                throw new \Exception("La necesidad con ese nombre ya existe");
            }else{
                $this->db
                    ->query("
                        UPDATE Necesidad
                        SET nombre = :necesidad
                        WHERE id_necesidad = :id
                    ")
                    ->bind(':necesidad', $nombre)
                    ->bind(':id', $id)
                    ->execute();

                return $this->findByNombre($nombre);
            }
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la necesidad");
        }
    }
}