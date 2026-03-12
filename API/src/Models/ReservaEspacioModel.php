<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaEspacioModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function getByEspacio(string $idEspacio): array
    {
        try {
            $sql = "
                SELECT r.id_reserva, r.asignatura, r.autorizada,
                       r.observaciones, r.grupo, r.profesor,
                       r.inicio, r.fin, re.actividad
                FROM Reserva r
                JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                WHERE re.id_espacio = :idEspacio AND (r.autorizada IS NULL OR r.autorizada = 1)
                ORDER BY r.inicio ASC
            ";

            return $this->db
                ->query($sql)
                ->bind(':idEspacio', $idEspacio)
                ->fetchAll();

        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del espacio");
        }
    }

    public function createReserva(array $data): int
    {
        $this->db
            ->query("
                INSERT INTO Reserva (
                    asignatura, autorizada, observaciones, grupo,
                    profesor, f_creacion, inicio, fin,
                    id_usuario, id_usuario_autoriza, tipo
                )
                VALUES (
                    :asignatura, :autorizada, :observaciones, :grupo,
                    :profesor, NOW(), :inicio, :fin,
                    :id_usuario, NULL, 'Reserva_espacio'
                )
            ")
            ->bind(':asignatura', $data['asignatura'])
            ->bind(':observaciones', $data['observaciones'] ?? '')
            ->bind(':grupo', $data['grupo'])
            ->bind(':profesor', $data['profesor'])
            ->bind(':inicio', $data['inicio'])
            ->bind(':fin', $data['fin'])
            ->bind(':id_usuario', $data['id_usuario'])
            ->bind(':autorizada', $data['autorizada'] ?? null)
            ->execute();

        return (int) $this->db->lastId();
    }

    public function createReservaEspacio(
        int $idReserva,
        string $actividad,
        string $idEspacio
    ): void {
        $this->db
            ->query("
                INSERT INTO Reserva_espacio (
                    id_reserva, actividad, id_espacio
                )
                VALUES (
                    :id_reserva, :actividad, :id_espacio
                )
            ")
            ->bind(':id_reserva', $idReserva)
            ->bind(':actividad', $actividad)
            ->bind(':id_espacio', $idEspacio)
            ->execute();
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM Reserva WHERE id_reserva = :id";

        $result = $this->db
            ->query($sql)
            ->bind(':id', $id)
            ->fetch();

        return $result ?: null;
    }

    public function updateReserva(int $id, array $data): void
{
    $this->db
        ->query("
            UPDATE Reserva
            SET asignatura = :asignatura,
                observaciones = :observaciones,
                grupo = :grupo,
                profesor = :profesor
            WHERE id_reserva = :id
        ")
        ->bind(':asignatura', $data['asignatura'])
        ->bind(':observaciones', $data['observaciones'] ?? '')
        ->bind(':grupo', $data['grupo'])
        ->bind(':profesor', $data['profesor'])
        ->bind(':id', $id)
        ->execute();

    $this->db
        ->query("
            UPDATE Reserva_espacio
            SET actividad = :actividad
            WHERE id_reserva = :id
        ")
        ->bind(':actividad', $data['actividad'])
        ->bind(':id', $id)
        ->execute();
}
}