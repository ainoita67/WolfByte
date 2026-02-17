<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class LiberacionPuntualModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todas las liberaciones puntuales activas
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Liberacion_puntual")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener liberaciones puntuales");
        }
    }

    /**
     * Obtener liberación puntual por ID
     */
    public function findById(int $id): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Liberacion_puntual WHERE id_liberacion_puntual = :id")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la liberación puntual");
        }
    }

    /**
     * Obtener liberación puntual por ID de recurso
     */
    public function findByIdRecurso(string $id_recurso): array|false
    {
        try {
            return $this->db
                ->query("SELECT l.id_liberacion_puntual as id, l.inicio, l.fin, l.comentario, l.id_reserva, l.id_reserva_permanente, p.id_recurso 
                    FROM Liberacion_puntual l 
                    JOIN Reserva_permanente p on l.id_reserva_permanente = p.id_reserva_permanente
                    WHERE p.id_recurso = :id_recurso")
                ->bind(':id_recurso', $id_recurso)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la liberación puntual");
        }
    }

    /**
     * Obtener liberación puntual por ID de usuario
     */
    public function findByIdUsuario(int $id_usuario): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Liberacion_puntual l JOIN Reserva r ON l.id_reserva = r.id_reserva WHERE r.id_usuario = :id_usuario")
                ->bind(':id_usuario', $id_usuario)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la liberación puntual");
        }
    }

    /**
     * Crear liberación puntual
     */
    public function create(array $data): array
    {
        try {
            $this->db
                ->query("
                    INSERT INTO Liberacion_puntual (inicio, fin, comentario, id_reserva, id_reserva_permanente)
                    VALUES (:inicio, :fin, :comentario, :id_reserva, :id_reserva_permanente)
                ")
                ->bind(':inicio',                   $data['inicio'])
                ->bind(':fin',                      $data['fin'])
                ->bind(':comentario',               $data['comentario'] ?? null)
                ->bind(':id_reserva',               $data['id_reserva'] ?? null)
                ->bind(':id_reserva_permanente',    $data['id_reserva_permanente'])
                ->execute();

            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la liberación puntual");
        }
    }

    /**
     * Crear liberación puntual por ID de reserva
     */
    public function createByReserva(int $id_reserva, array $data): array
    {
        try {
            $this->db
                ->query("
                    INSERT INTO Liberacion_puntual (inicio, fin, comentario, id_reserva, id_reserva_permanente)
                    VALUES (:inicio, :fin, :comentario, :id_reserva, :id_reserva_permanente)
                ")
                ->bind(':inicio',                   $data['inicio'])
                ->bind(':fin',                      $data['fin'])
                ->bind(':comentario',               $data['comentario'] ?? null)
                ->bind(':id_reserva',               $id_reserva)
                ->bind(':id_reserva_permanente',    $data['id_reserva_permanente'])
                ->execute();

            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la liberación puntual");
        }
    }

    /**
     * Actualizar liberación puntual
     */
    public function update(int $id, array $data): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Liberacion_puntual SET
                        inicio = :inicio,
                        fin = :fin,
                        comentario = :comentario,
                        id_reserva = :id_reserva,
                        id_reserva_permanente = :id_reserva_permanente
                    WHERE id_liberacion_puntual = :id
                ")
                ->bind(':id',                       $id)
                ->bind(':inicio',                   $data['inicio'])
                ->bind(':fin',                      $data['fin'])
                ->bind(':comentario',               $data['comentario'] ?? null)
                ->bind(':id_reserva',               $data['id_reserva'] ?? null)
                ->bind(':id_reserva_permanente',    $data['id_reserva_permanente'])
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la liberación puntual");
        }
    }

    /**
     * Eliminar liberación puntual
     */
    public function delete(int $id): array
    {
        try {
            $liberacion = $this->findById($id);
            if (!$liberacion) {
                throw new \Exception("Liberación puntual no encontrada");
            }else{
                $this->db
                    ->query("DELETE FROM Liberacion_puntual WHERE id_liberacion_puntual = :id")
                    ->bind(':id', $id)
                    ->execute();

                return ["mensaje" => "Liberación puntual eliminada correctamente"];
            }
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar la liberación puntual");
        }
    }
}