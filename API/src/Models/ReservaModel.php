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

    /**
     * Obtener reservas
     */
    public function getAll(): array
    {
        try {
            return $this->db
                ->query("SELECT * FROM Reserva")
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas");
        }
    }

    /**
     * Obtener reservas por usuario
     */
    public function getByUsuario(int $idUsuario): array
    {
        try {
            return $this->db
                ->query("
                    SELECT *
                    FROM Reserva
                    WHERE id_usuario = :id_usuario
                    ORDER BY inicio DESC
                ")
                ->bind(':id_usuario', $idUsuario)
                ->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener reservas del usuario");
        }
    }

    /**
     * Obtener una reserva por ID
     */
    public function findById(int $id): array|false
    {
        return $this->db
            ->query("SELECT * FROM Reserva WHERE id_reserva = :id")
            ->bind(':id', $id)
            ->fetch();
    }

    /**
     * Obtener reservas pendientes de autorizar
     */
    public function getReservasPendientes(): array|false
    {
        return $this->db
            ->query("
                SELECT
                    r.id_reserva,
                    r.autorizada,
                    r.tipo,
                    r.f_creacion,
                    r.inicio,
                    r.fin,
                    rec.descripcion AS recurso,
                    r.asignatura,
                    r.grupo,
                    r.profesor,
                    rec.id_recurso,
                    NULL AS unidades,
                    NULL AS usaenespacio,
                    re.actividad,
                    n.nombre AS necesidades,
                    r.observaciones,
                    u.id_usuario,
                    u.nombre AS nombreusuario
                FROM Reserva r
                JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                JOIN Recurso rec ON rec.id_recurso = re.id_espacio
                LEFT JOIN Necesidad_R_espacio nre ON re.id_reserva=nre.id_reserva_espacio
                LEFT JOIN Necesidad n ON nre.id_necesidad=n.id_necesidad
                JOIN Usuario u ON r.id_usuario = u.id_usuario
                WHERE r.tipo = 'Reserva_espacio' AND r.autorizada IS NULL

                UNION ALL

                SELECT
                    r.id_reserva,
                    r.autorizada,
                    r.tipo,
                    r.f_creacion,
                    r.inicio,
                    r.fin,
                    rec.descripcion AS recurso,
                    r.asignatura,
                    r.grupo,
                    r.profesor,
                    rec.id_recurso,
                    rp.unidades,
                    rp.usaenespacio,
                    NULL AS actividad,
                    NULL AS necesidades,
                    r.observaciones,
                    u.id_usuario,
                    u.nombre AS nombreusuario
                FROM Reserva r
                JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                JOIN Recurso rec ON rec.id_recurso = rp.id_material
                JOIN Usuario u ON r.id_usuario = u.id_usuario
                WHERE r.tipo = 'Reserva_material' AND r.autorizada IS NULL
            ")
            ->fetchAll();
    }

    /**
     * Obtener reservas próximas al día de hoy
     */
    public function getReservasProximas(): array|false
    {
        return $this->db
            ->query("
                SELECT *
                FROM(
                    SELECT
                        r.id_reserva,
                        r.autorizada,
                        r.tipo,
                        r.f_creacion,
                        r.inicio,
                        r.fin,
                        rec.descripcion AS recurso,
                        r.asignatura,
                        r.grupo,
                        r.profesor,
                        rec.id_recurso,
                        NULL AS unidades,
                        NULL AS usaenespacio,
                        re.actividad,
                        n.nombre AS necesidades,
                        r.observaciones,
                        u.id_usuario,
                        u.nombre AS nombreusuario
                    FROM Reserva r
                    JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                    JOIN Recurso rec ON rec.id_recurso = re.id_espacio
                    LEFT JOIN Necesidad_R_espacio nre ON re.id_reserva=nre.id_reserva_espacio
                    LEFT JOIN Necesidad n ON nre.id_necesidad=n.id_necesidad
                    JOIN Usuario u ON r.id_usuario = u.id_usuario
                    WHERE r.tipo = 'Reserva_espacio' AND r.inicio>NOW() AND r.autorizada=1

                    UNION ALL

                    SELECT
                        r.id_reserva,
                        r.autorizada,
                        r.tipo,
                        r.f_creacion,
                        r.inicio,
                        r.fin,
                        rec.descripcion AS recurso,
                        r.asignatura,
                        r.grupo,
                        r.profesor,
                        rec.id_recurso,
                        rp.unidades,
                        rp.usaenespacio,
                        NULL AS actividad,
                        NULL AS necesidades,
                        r.observaciones,
                        u.id_usuario,
                        u.nombre AS nombreusuario
                    FROM Reserva r
                    JOIN Reserva_Portatiles rp ON r.id_reserva = rp.id_reserva_material
                    JOIN Recurso rec ON rec.id_recurso = rp.id_material
                    JOIN Usuario u ON r.id_usuario = u.id_usuario
                    WHERE r.tipo = 'Reserva_material' AND r.inicio>NOW() AND r.autorizada=1
                ) union_result ORDER BY inicio, id_reserva;
            ")
            ->fetchAll();
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
    }

    public function getReservasSalonActos(): array
    {
        return $this->db
            ->query("
                SELECT 
                    r.id_reserva,
                    r.asignatura,
                    r.tipo,
                    r.autorizada,
                    r.observaciones,
                    r.grupo,
                    r.profesor,
                    r.f_creacion,
                    r.inicio,
                    r.fin,
                    r.id_usuario,
                    r.id_usuario_autoriza,
                    r.tipo,
                    u.nombre,
                    u.apellidos,
                    re.actividad,
                    re.id_espacio
                FROM Reserva r
                JOIN Usuario u ON r.id_usuario = u.id_usuario
                JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
                WHERE re.id_espacio = 'salon'
                ORDER BY r.inicio
            ")
            ->fetchAll();
    }
}