<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

class ReservaEspacioModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function getAll(): array
    {
        return $this->db
            ->query("SELECT * FROM Reserva_espacio ORDER BY id_reserva_espacio DESC")
            ->fetchAll();
    }

    public function getByEspacio($idEspacio): array
    {
        return $this->db
            ->query("SELECT * FROM Reserva_espacio WHERE id_espacio = :id_espacio ORDER BY id_reserva_espacio DESC")
            ->bind(':id_espacio', $idEspacio)
            ->fetchAll();
    }

    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT * FROM Reserva_espacio WHERE id_reserva_espacio = :id")
            ->bind(':id', $id)
            ->fetch();
    }

    public function create(array $data): array
    {
        $this->db->query("
            INSERT INTO Reserva_espacio (
                id_reserva_espacio,
                actividad,
                id_espacio
            ) VALUES (
                :id_reserva_espacio,
                :actividad,
                :id_espacio
            )
        ")
        ->bind(':id_reserva_espacio', $data['id_reserva_espacio'])
        ->bind(':actividad', $data['actividad'])
        ->bind(':id_espacio', $data['id_espacio'])
        ->execute();

        return $this->findById((int)$data['id_reserva_espacio']);
    }


    public function update(int $id, array $data): array
    {
        $this->db->query("
            UPDATE Reserva_espacio
            SET actividad = :actividad,
                id_espacio = :id_espacio
            WHERE id_reserva_espacio = :id
        ")
        ->bind(':actividad', $data['actividad'])
        ->bind(':id_espacio', $data['id_espacio'])
        ->bind(':id', $id)
        ->execute();

        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $this->db
            ->query("DELETE FROM Reserva_espacio WHERE id_reserva_espacio = :id")
            ->bind(':id', $id)
            ->execute();
    }
}
