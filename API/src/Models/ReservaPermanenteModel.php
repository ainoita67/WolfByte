<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaPermanenteModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtener todas las reservas permanentes activas
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT rp.*, r.tipo FROM Reserva_permanente rp JOIN Recurso r ON r.id_recurso=rp.id_recurso WHERE rp.activo=1")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas permanentes");
        }
    }

    /**
     * Obtener todas las reservas permanentes inactivas
     */
    public function getAllInactivas(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva_permanente WHERE activo=0")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas permanentes");
        }
    }

    /**
     * Obtener reserva permanente por ID
     */
    public function findById(int $id): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva_permanente WHERE id_reserva_permanente = :id")
                ->bind(':id', $id)
                ->fetch();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la reserva permanente");
        }
    }

    /**
     * Obtener reserva permanente por ID de recurso
     */
    public function findByIdRecurso(string $id_recurso): array|false
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva_permanente WHERE id_recurso = :id_recurso AND activo=1")
                ->bind(':id_recurso', $id_recurso)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al buscar la reserva permanente");
        }
    }

    /**
     * Crear reserva permanente
     */
    public function create(array $data): array
    {
        try {
            $this->db
                ->query("
                    INSERT INTO Reserva_permanente (inicio, fin, comentario, activo, id_recurso, dia_semana)
                    VALUES (:inicio, :fin, :comentario, :activo, :id_recurso, :dia_semana)
                ")
                ->bind(':inicio',       $data['inicio'])
                ->bind(':fin',          $data['fin'])
                ->bind(':comentario',   $data['comentario'] ?? null)
                ->bind(':activo',       $data['activo'])
                ->bind(':id_recurso',   $data['id_recurso'])
                ->bind(':dia_semana',    $data['dia_semana'])
                ->execute();

            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la reserva permanente".$e->getMessage());
        }
    }

    /**
     * Actualizar reserva permanente
     */
    public function update(int $id, array $data): bool
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva_permanente SET
                        inicio = :inicio,
                        fin = :fin,
                        comentario = :comentario,
                        activo = :activo,
                        id_recurso = :id_recurso,
                        dia_semana = :dia_semana,
                        unidades = :unidades
                    WHERE id_reserva_permanente = :id
                ")
                ->bind(':id',           $id)
                ->bind(':inicio',       $data['inicio'])
                ->bind(':fin',          $data['fin'])
                ->bind(':comentario',   $data['comentario'] ?? '')
                ->bind(':activo',       $data['activo'])
                ->bind(':id_recurso',   $data['id_recurso'])
                ->bind(':dia_semana',   $data['dia_semana'])
                ->bind(':unidades',     $data['unidades'] ?? null)
                ->execute();

            return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'] > 0;
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la reserva permanente");
        }
    }

    /**
     * Activar reserva permanente
     */
    public function activar(int $id): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva_permanente SET
                        activo = :activo
                    WHERE id_reserva_permanente = :id
                ")
                ->bind(':id',           $id)
                ->bind(':activo',       1)
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al activar la reserva permanente");
        }
    }

    /**
     * Desactivar reserva permanente
     */
    public function desactivar(int $id): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva_permanente SET
                        activo = :activo
                    WHERE id_reserva_permanente = :id
                ")
                ->bind(':id',           $id)
                ->bind(':activo',       0)
                ->execute();

            return $this->findById($id);
        } catch (PDOException $e) {
            throw new \Exception("Error al desactivar la reserva permanente");
        }
    }

    /**
     * Desactivar todas las reservas permanentes
     */
    public function desactivartodas(): array
    {
        try {
            $this->db
                ->query("
                    UPDATE Reserva_permanente SET
                        activo = :activo
                    WHERE activo = 1
                ")
                ->bind(':activo',   0)
                ->execute();

            return $this->getAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al desactivar las reservas permanentes");
        }
    }
}