<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

class LogAccionesModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }



    /**
     * Obtener todos los logs
     */
    public function all(): array
    {
        return $this->db
            ->query("SELECT
                    l.id_log,
                    l.fecha,
                    tl.id_tipo_log,
                    tl.tipo,
                    l.id_usuario,
                    l.id_incidencia,
                    l.id_reserva,
                    l.id_recurso,
                    l.id_reserva_permanente,
                    l.id_liberacion_puntual,
                    l.id_usuario_actor,
                    u.nombre AS usuario,
                    ua.nombre AS usuarioactor
                FROM Log l
                JOIN Tipo_log tl ON l.id_tipo_log=tl.id_tipo_log
                JOIN Usuario ua ON l.id_usuario_actor=ua.id_usuario
                LEFT JOIN Usuario u ON l.id_usuario=u.id_usuario
                LEFT JOIN Incidencia i ON l.id_incidencia=i.id_incidencia
                LEFT JOIN Reserva r ON l.id_reserva=r.id_reserva
                LEFT JOIN Recurso rec ON l.id_recurso=rec.id_recurso
                LEFT JOIN Liberacion_puntual lp ON l.id_liberacion_puntual=lp.id_liberacion_puntual
                ORDER BY l.id_log
            ")
            ->fetchAll();
    }



    /**
     * Obtener todos los tipos de logs
     */
    public function allTipoLog(): array
    {
        return $this->db
            ->query("SELECT * FROM Tipo_log")
            ->fetchAll();
    }



    /**
     * Obtener tipo de log por tipo
     */
    public function findTipoLogByTipo(string $tipo): array|false
    {
        return $this->db
            ->query("SELECT * FROM Tipo_log WHERE tipo=:tipo")
            ->bind(':tipo', $tipo)
            ->fetch();
    }



    /**
     * Obtener tipo de log por tipo
     */
    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT * FROM Log WHERE id_log=:id")
            ->bind(':id', $id)
            ->fetch();
    }



    /**
     * Crear un nuevo log
     */
    public function create(int $tipo, array $data): int
    {
        $this->db
            ->query("INSERT INTO Log (fecha, id_tipo_log, id_usuario, id_incidencia, id_reserva, id_recurso, id_reserva_permanente, id_liberacion_puntual, id_usuario_actor)
            VALUES (NOW(), :id_tipo_log, :id_usuario, :id_incidencia, :id_reserva, :id_recurso, :id_reserva_permanente, :id_liberacion_puntual, :id_usuario_actor)")
            ->bind(':id_tipo_log', $tipo)
            ->bind(':id_usuario', $data['id_usuario'] ?? null)
            ->bind(':id_incidencia', $data['id_incidencia'] ?? null)
            ->bind(':id_reserva', $data['id_reserva'] ?? null)
            ->bind(':id_recurso', $data['id_recurso'] ?? null)
            ->bind(':id_reserva_permanente', $data['id_reserva_permanente'] ?? null)
            ->bind(':id_liberacion_puntual', $data['id_liberacion_puntual'] ?? null)
            ->bind(':id_usuario_actor', $data['id_usuario_actor'])
            ->execute();
        
        return (int)$this->db->lastId();
    }
}