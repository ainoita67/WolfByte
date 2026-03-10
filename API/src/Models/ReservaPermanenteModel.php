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
                ->query("SELECT * FROM Reserva_permanente WHERE activo=1")
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
                    INSERT INTO Reserva_permanente (dia_semana, inicio, fin, comentario, activo, id_recurso, unidades)
                    VALUES (:dia_semana, :inicio, :fin, :comentario, 1 , :id_recurso, :unidades)
                ")
                ->bind(':dia_semana',   $data['dia_semana'])
                ->bind(':inicio',       $data['inicio'])
                ->bind(':fin',          $data['fin'])
                ->bind(':comentario',   $data['comentario'] ?? null)
                ->bind(':id_recurso',   $data['id_recurso'])
                ->bind(':unidades',     $data['unidades'])
                ->execute();

            return $this->findById((int)$this->db->lastId());
        } catch (PDOException $e) {
            throw new \Exception("Error al crear la reserva permanente");
        }
    }

    /**
     * Actualizar reserva permanente
     */
    public function update(int $id, array $data): array
    {
        $this->db
            ->query("
                UPDATE Reserva_permanente SET
                    inicio = :inicio,
                    fin = :fin,
                    dia_semana = :dia_semana,
                    comentario = :comentario,
                    unidades = :unidades
                WHERE id_reserva_permanente = :id
            ")
            ->bind(':id',           $id)
            ->bind(':inicio',       $data['inicio'])
            ->bind(':fin',          $data['fin'])
            ->bind(':comentario',   $data['comentario'])
            ->bind(':unidades',       $data['unidades'])
            ->bind(':dia_semana',   $data['dia_semana'])
            ->execute();

        return $this->findById($id);
    }


    public function unidadesReservadas(string $id_recurso, int $dia_semana, string $inicio, string $fin): int
    {
        try {
            $result = $this->db
                ->query("
                    SELECT SUM(unidades) as total_unidades
                    FROM Reserva_permanente
                    WHERE id_recurso = :id_recurso
                    AND dia_semana = :dia_semana
                    AND activo = 1
                    AND (
                        (inicio < :fin AND fin > :inicio)
                    )
                ")
                ->bind(':id_recurso',   $id_recurso)
                ->bind(':dia_semana',   $dia_semana)
                ->bind(':inicio',       $inicio)
                ->bind(':fin',          $fin)
                ->fetch();

            return (int)$result['total_unidades'];
        } catch (PDOException $e) {
            throw new \Exception("Error al calcular las unidades reservadas");
        }
    }

        /**
     * Activar reserva permanente
     */
    public function setActive(int $id): bool
    {
        $this->db
            ->query("UPDATE Reserva_permanente SET activo = true WHERE id_reserva_permanente = :id")
            ->bind(':id', $id)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Desactivar reserva permanente
     */
    public function setInactive(int $id): bool
    {
        $this->db
            ->query("UPDATE Reserva_permanente SET activo = false WHERE id_reserva_permanente = :id")
            ->bind(':id', $id)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Verificar si reserva permanente está activo
     */
    public function isActive(int $id): bool
    {
        $result = $this->db
            ->query("SELECT activo FROM Reserva_permanente WHERE id_reserva_permanente = :id")
            ->bind(':id', $id)
            ->fetch();

        return $result ? (bool) $result['activo'] : false;
    }

        /**
     * Desactivar todas las reservas permanentes
     */
    public function desactivar(int $id): bool
    {
        $this->db
            ->query("UPDATE Reserva_permanente SET activo = false")
            ->execute();

        return $this->db->rowCount() > 0;
    }
}