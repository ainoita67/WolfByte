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
     * Crear una nueva necesidad de reserva
     */
    public function create(array $data): int
    {
        $this->db
            ->query("INSERT INTO NecesidadReserva (usuario_id, fecha, hora, cantidad_personas, tipo_servicio, notas)
                     VALUES (:usuario_id, :fecha, :hora, :cantidad_personas, :tipo_servicio, :notas)")
            ->bind(':usuario_id', $data['usuario_id'])
            ->bind(':fecha', $data['fecha'])
            ->bind(':hora', $data['hora'])
            ->bind(':cantidad_personas', $data['cantidad_personas'])
            ->bind(':tipo_servicio', $data['tipo_servicio'])
            ->bind(':notas', $data['notas'] ?? null)
            ->execute();

        return (int)$this->db->lastId();
    }

    /**
     * Obtener necesidad de reserva por ID
     */
    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT * FROM NecesidadReserva WHERE id = :id")
            ->bind(':id', $id)
            ->fetch();
    }

    /**
     * Obtener todas las necesidades de un usuario
     */
    public function findByUsuario(int $usuarioId): array
    {
        return $this->db
            ->query("SELECT * FROM NecesidadReserva WHERE usuario_id = :usuario_id ORDER BY fecha, hora")
            ->bind(':usuario_id', $usuarioId)
            ->fetchAll();
    }

    /**
     * Eliminar necesidad de reserva de un usuario
     */
    public function deleteByUsuario(int $usuarioId, int $id): bool
    {
        $this->db
            ->query("DELETE FROM NecesidadReserva WHERE id = :id AND usuario_id = :usuario_id")
            ->bind(':id', $id)
            ->bind(':usuario_id', $usuarioId)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Actualizar necesidad de reserva
     */
    public function update(int $id, array $data): bool
    {
        $this->db
            ->query("UPDATE NecesidadReserva
                     SET fecha = :fecha,
                         hora = :hora,
                         cantidad_personas = :cantidad_personas,
                         tipo_servicio = :tipo_servicio,
                         notas = :notas
                     WHERE id = :id")
            ->bind(':fecha', $data['fecha'])
            ->bind(':hora', $data['hora'])
            ->bind(':cantidad_personas', $data['cantidad_personas'])
            ->bind(':tipo_servicio', $data['tipo_servicio'])
            ->bind(':notas', $data['notas'] ?? null)
            ->bind(':id', $id)
            ->execute();

        return $this->db->rowCount() > 0;
    }

    public function findAll(): array
    {
        return $this->db->query("SELECT * FROM NecesidadReserva ORDER BY fecha, hora")->fetchAll();
    }

}
