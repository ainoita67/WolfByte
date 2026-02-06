<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class ReservaModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function getAll(): array
    {
        return $this->db
            ->query("SELECT * FROM Reserva ORDER BY inicio DESC")
            ->fetchAll();
    }

    public function getByUsuario(int $idUsuario): array
    {
        return $this->db
            ->query("SELECT * FROM Reserva WHERE id_usuario = :id_usuario ORDER BY inicio DESC")
            ->bind(':id_usuario', $idUsuario)
            ->fetchAll();
    }

    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT * FROM Reserva WHERE id_reserva = :id")
            ->bind(':id', $id)
            ->fetch();
    }

   public function updateFechas(
    int $idReserva,
    string $inicio,
    string $fin
): void {
    $this->db
        ->query("
            UPDATE Reserva
            SET inicio = :inicio,
                fin = :fin
            WHERE id_reserva = :id
        ")
        ->bind(':inicio', $inicio)
        ->bind(':fin', $fin)
        ->bind(':id', $idReserva)
        ->execute();

    public function create(array $data): array
    {
        $this->db->query("
            INSERT INTO Reserva (
                asignatura,
                autorizada,
                observaciones,
                grupo,
                profesor,
                f_creacion,
                inicio,
                fin,
                id_usuario,
                id_usuario_autoriza,
                tipo
            ) VALUES (
                :asignatura,
                :autorizada,
                :observaciones,
                :grupo,
                :profesor,
                :f_creacion,
                :inicio,
                :fin,
                :id_usuario,
                :id_usuario_autoriza,
                :tipo
            )
        ")
        ->bind(':asignatura', $data['asignatura'] ?? null)
        ->bind(':autorizada', $data['autorizada'] ?? 0)
        ->bind(':observaciones', $data['observaciones'] ?? null)
        ->bind(':grupo', $data['grupo'] ?? null)
        ->bind(':profesor', $data['profesor'] ?? null)
        ->bind(':f_creacion', $data['f_creacion'] ?? date('Y-m-d H:i:s'))
        ->bind(':inicio', $data['inicio'])
        ->bind(':fin', $data['fin'])
        ->bind(':id_usuario', $data['id_usuario'])
        ->bind(':id_usuario_autoriza', $data['id_usuario_autoriza'] ?? null)
        ->bind(':tipo', $data['tipo'] ?? 'Reserva_espacio')
        ->execute();

        return $this->findById((int)$this->db->lastId());
    }

    public function update(int $id, array $data): array
    {
        $this->db->query("
            UPDATE Reserva
            SET asignatura = :asignatura,
                autorizada = :autorizada,
                observaciones = :observaciones,
                grupo = :grupo,
                profesor = :profesor,
                f_creacion = :f_creacion,
                inicio = :inicio,
                fin = :fin,
                id_usuario = :id_usuario,
                id_usuario_autoriza = :id_usuario_autoriza,
                tipo = :tipo
            WHERE id_reserva = :id
        ")
        ->bind(':asignatura', $data['asignatura'] ?? null)
        ->bind(':autorizada', $data['autorizada'] ?? 0)
        ->bind(':observaciones', $data['observaciones'] ?? null)
        ->bind(':grupo', $data['grupo'] ?? null)
        ->bind(':profesor', $data['profesor'] ?? null)
        ->bind(':f_creacion', $data['f_creacion'] ?? date('Y-m-d H:i:s'))
        ->bind(':inicio', $data['inicio'])
        ->bind(':fin', $data['fin'])
        ->bind(':id_usuario', $data['id_usuario'])
        ->bind(':id_usuario_autoriza', $data['id_usuario_autoriza'] ?? null)
        ->bind(':tipo', $data['tipo'] ?? 'Reserva_espacio')
        ->bind(':id', $id)
        ->execute();

        return $this->findById($id);
    }


    public function delete(int $id): void
    {
        $this->db
            ->query("DELETE FROM Reserva WHERE id_reserva = :id")
            ->bind(':id', $id)
            ->execute();
    }
}

public function getReservasSalonActos(): array
{
    return $this->db
        ->query("
            SELECT r.*, u.nombre, u.apellidos 
            FROM Reserva r
            JOIN Usuario u ON r.id_usuario = u.id_usuario
            WHERE r.id_aula = 1  -- Ajusta el ID del salón de actos
            ORDER BY r.inicio
        ")
        ->fetchAll();
}

}