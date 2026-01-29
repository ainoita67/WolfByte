<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

class NecesidadReservaModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Crear relación reserva-espacio ↔ necesidad
     */
    public function create(array $data): bool
    {
        $this->db
            ->query("INSERT INTO necesidad_r_espacio (id_reserva_espacio, id_necesidad)
                     VALUES (:id_reserva_espacio, :id_necesidad)")
            ->bind(':id_reserva_espacio', $data['id_reserva_espacio'])
            ->bind(':id_necesidad', $data['id_necesidad'])
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Obtener una relación específica
     */
    public function findOne(int $idReservaEspacio, int $idNecesidad): array|false
    {
        return $this->db
            ->query("SELECT *
                     FROM necesidad_r_espacio
                     WHERE id_reserva_espacio = :id_reserva_espacio
                       AND id_necesidad = :id_necesidad")
            ->bind(':id_reserva_espacio', $idReservaEspacio)
            ->bind(':id_necesidad', $idNecesidad)
            ->fetch();
    }

    /**
     * Obtener necesidades de una reserva
     */
    public function findByReserva(int $idReserva): array
    {
        return $this->db
            ->query("SELECT n.id_necesidad, n.nombre
                     FROM necesidad_r_espacio nr
                     JOIN necesidad n ON n.id_necesidad = nr.id_necesidad
                     JOIN reserva_espacio re ON re.id_reserva_espacio = nr.id_reserva_espacio
                     WHERE re.id_reserva = :id_reserva")
            ->bind(':id_reserva', $idReserva)
            ->fetchAll();
    }

    /**
     * Actualizar una necesidad por otra
     */
    public function update(int $idReservaEspacio, int $idNecesidadActual, int $idNecesidadNueva): bool
    {
        $this->db
            ->query("UPDATE necesidad_r_espacio
                     SET id_necesidad = :id_necesidad_nueva
                     WHERE id_reserva_espacio = :id_reserva_espacio
                       AND id_necesidad = :id_necesidad_actual")
            ->bind(':id_necesidad_nueva', $idNecesidadNueva)
            ->bind(':id_reserva_espacio', $idReservaEspacio)
            ->bind(':id_necesidad_actual', $idNecesidadActual)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Eliminar una necesidad de una reserva
     */
    public function delete(int $idReservaEspacio, int $idNecesidad): bool
    {
        $this->db
            ->query("DELETE FROM necesidad_r_espacio
                     WHERE id_reserva_espacio = :id_reserva_espacio
                       AND id_necesidad = :id_necesidad")
            ->bind(':id_reserva_espacio', $idReservaEspacio)
            ->bind(':id_necesidad', $idNecesidad)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Eliminar todas las necesidades de una reserva-espacio
     */
    public function deleteAllByReservaEspacio(int $idReservaEspacio): bool
    {
        $this->db
            ->query("DELETE FROM necesidad_r_espacio
                     WHERE id_reserva_espacio = :id_reserva_espacio")
            ->bind(':id_reserva_espacio', $idReservaEspacio)
            ->execute();

        return $this->db->rowCount() > 0;
    }
}
