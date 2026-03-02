<?php
declare(strict_types=1);

namespace Models;

use Core\DB;
use PDOException;

class EspacioModel
{
    private DB $db;
    public function __construct()
    {
        $this->db = new DB();
    }

    // Obtener todos los Espacios
    public function getAll(): array
    {
        return $this->db
            ->query("SELECT 
                            r.id_recurso,
                            r.descripcion,
                            r.tipo,
                            r.activo,
                            r.especial,
                            r.numero_planta,
                            e.nombre_edificio,
                            es.es_aula
                        FROM Recurso r
                        LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
                        LEFT JOIN Espacio es ON r.id_recurso = es.id_espacio
                        WHERE r.tipo = 'Espacio'
                        ORDER BY r.id_recurso;")
            ->fetchAll();
    }

    // Obtener Espacio por ID
    public function findById(string $id): ?array
    {
        $result = $this->db
            ->query("SELECT 
                            r.id_recurso,
                            r.descripcion,
                            r.tipo,
                            r.activo,
                            r.especial,
                            r.numero_planta,
                            p.nombre_planta,
                            e.nombre_edificio,
                            es.es_aula
                        FROM Recurso r
                        LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
                        LEFT JOIN Planta p ON p.numero_planta = r.numero_planta
                        LEFT JOIN Espacio es ON r.id_recurso = es.id_espacio
                        WHERE r.tipo = 'Espacio' AND r.id_recurso=:id
                        ORDER BY r.id_recurso;")
            ->bind(':id', $id)
            ->fetch();

        return $result ?: null;
    }

    // Crear Espacio
    public function create(array $data): int|false
    {
        try {
            $this->db->beginTransaction();

            $this->db
                ->query("INSERT INTO Recurso (id_recurso, descripcion, tipo, activo, especial, numero_planta, id_edificio)
                        VALUES (:id_recurso, :descripcion, 'Espacio', :activo, :especial, :numero_planta, :id_edificio)")
                ->bind(':id_recurso', $data['id_recurso'])
                ->bind(':descripcion', $data['descripcion'])
                ->bind(':activo', $data['activo'] ?? 1)
                ->bind(':especial', $data['especial'] ?? 0)
                ->bind(':numero_planta', $data['numero_planta'])
                ->bind(':id_edificio', $data['id_edificio'])
                ->execute();

            // Insertar en Espacio
            $this->db
                ->query("INSERT INTO Espacio (id_espacio, es_aula)
                        VALUES (:id_espacio, :es_aula)")
                ->bind(':id_espacio', $data['id_recurso'])
                ->bind(':es_aula', $data['es_aula'])
                ->execute();



            $this->db->commit();
            return 1; // Retorna 1 para indicar éxito

        } catch (PDOException $e) {
            $this->db->rollback();
            error_log("Error al crear espacio: " . $e->getMessage());
            return false;
        }
    }

    public function getAllAulas(): array
    {
        return $this->db
            ->query("SELECT r.id_recurso, r.descripcion, p.nombre_planta, ed.nombre_edificio 
                FROM Recurso r 
                JOIN Espacio e ON r.id_recurso=e.id_espacio 
                LEFT JOIN Planta p ON r.numero_planta=p.numero_planta AND r.id_edificio=p.id_edificio
                LEFT JOIN Edificio ed on ed.id_edificio=r.id_edificio 
                WHERE r.tipo = 'Espacio' AND e.es_aula=true AND r.activo=true
                ORDER BY r.numero_planta, r.id_recurso; ")
            ->fetchAll();
    }

    public function getOtrosEspacios(): array
    {
        return $this->db
            ->query("SELECT r.id_recurso, r.descripcion, p.nombre_planta, ed.nombre_edificio 
                FROM Recurso r 
                JOIN Espacio e ON r.id_recurso=e.id_espacio 
                LEFT JOIN Planta p ON r.numero_planta=p.numero_planta AND r.id_edificio=p.id_edificio
                LEFT JOIN Edificio ed on ed.id_edificio=r.id_edificio 
                WHERE r.tipo = 'Espacio' AND e.es_aula=false AND r.activo=true AND r.id_recurso != 'salon'
                ORDER BY r.numero_planta, r.id_recurso; ")
            ->fetchAll();
    }

    public function getCaracteristicasEspacio(string $id): ?array
    {
        $result = $this->db
            ->query("SELECT c.nombre
                FROM Caracteristica_Espacio ce
                JOIN Caracteristica c ON ce.id_caracteristica=c.id_caracteristica
                WHERE ce.id_espacio = :id;")
            ->bind(':id', $id)
            ->fetchAll();

        return array_column($result, 'nombre') ?: null;
    }

    public function getAulasLibres($inicio, $fin, $dia_semana, $hora_inicio, $hora_fin): array
    {
        return $this->db
            ->query("SELECT 
                r.id_recurso,
                r.descripcion,
                p.nombre_planta,
                ed.nombre_edificio,
                COUNT(DISTINCT res.id_reserva) AS total_reservas

            FROM Recurso r
            JOIN Espacio e 
                ON r.id_recurso = e.id_espacio
            LEFT JOIN Planta p 
                ON r.numero_planta = p.numero_planta 
                AND r.id_edificio = p.id_edificio
            LEFT JOIN Edificio ed 
                ON ed.id_edificio = r.id_edificio

            LEFT JOIN Reserva_espacio re
                ON re.id_espacio = r.id_recurso

            LEFT JOIN Reserva res
                ON res.id_reserva = re.id_reserva
                AND res.inicio < :res_fin
                AND res.fin > :res_inicio

            WHERE 
                r.tipo = 'Espacio'
                AND e.es_aula = true
                AND r.activo = true

                AND (
                    NOT EXISTS (
                        SELECT 1
                        FROM Reserva_permanente rp
                        WHERE rp.id_recurso = r.id_recurso
                        AND rp.activo = true
                        AND rp.dia_semana = :dia_semana
                        AND rp.inicio < :rp_fin
                        AND rp.fin > :rp_inicio
                    )

                    OR EXISTS (
                        SELECT 1
                        FROM Reserva_permanente rp
                        JOIN Liberacion_puntual lp 
                            ON lp.id_reserva_permanente = rp.id_reserva_permanente
                        WHERE rp.id_recurso = r.id_recurso
                        AND lp.inicio < :lp_fin
                        AND lp.fin > :lp_inicio
                    )
                )

            GROUP BY 
                r.id_recurso,
                r.descripcion,
                p.nombre_planta,
                ed.nombre_edificio

            ORDER BY 
                ed.nombre_edificio,
                p.numero_planta,
                r.id_recurso;
            ")
            ->bind(':dia_semana', $dia_semana)
            ->bind(':rp_inicio', $hora_inicio)
            ->bind(':rp_fin', $hora_fin)
            ->bind(':res_inicio', $inicio)
            ->bind(':res_fin', $fin)
            ->bind(':lp_inicio', $inicio)
            ->bind(':lp_fin', $fin)
            ->fetchAll();
    }

    public function getOtrosEspaciosLibres($inicio, $fin, $dia_semana, $hora_inicio, $hora_fin): array
    {
        return $this->db
            ->query("SELECT 
                r.id_recurso,
                r.descripcion,
                p.nombre_planta,
                ed.nombre_edificio,
                COUNT(DISTINCT res.id_reserva) AS total_reservas

            FROM Recurso r
            JOIN Espacio e 
                ON r.id_recurso = e.id_espacio
            LEFT JOIN Planta p 
                ON r.numero_planta = p.numero_planta 
                AND r.id_edificio = p.id_edificio
            LEFT JOIN Edificio ed 
                ON ed.id_edificio = r.id_edificio

            LEFT JOIN Reserva_espacio re
                ON re.id_espacio = r.id_recurso

            LEFT JOIN Reserva res
                ON res.id_reserva = re.id_reserva
                AND res.inicio < :res_fin
                AND res.fin > :res_inicio

            WHERE 
                r.tipo = 'Espacio'
                AND e.es_aula = false
                AND r.activo = true
                AND r.id_recurso != 'salon'

                AND (
                    NOT EXISTS (
                        SELECT 1
                        FROM Reserva_permanente rp
                        WHERE rp.id_recurso = r.id_recurso
                        AND rp.activo = true
                        AND rp.dia_semana = :dia_semana
                        AND rp.inicio < :rp_fin
                        AND rp.fin > :rp_inicio
                    )

                    OR EXISTS (
                        SELECT 1
                        FROM Reserva_permanente rp
                        JOIN Liberacion_puntual lp 
                            ON lp.id_reserva_permanente = rp.id_reserva_permanente
                        WHERE rp.id_recurso = r.id_recurso
                        AND lp.inicio < :lp_fin
                        AND lp.fin > :lp_inicio
                    )
                )

            GROUP BY 
                r.id_recurso,
                r.descripcion,
                p.nombre_planta,
                ed.nombre_edificio

            ORDER BY 
                ed.nombre_edificio,
                p.numero_planta,
                r.id_recurso;
            ")
            ->bind(':dia_semana', $dia_semana)
            ->bind(':rp_inicio', $hora_inicio)
            ->bind(':rp_fin', $hora_fin)
            ->bind(':res_inicio', $inicio)
            ->bind(':res_fin', $fin)
            ->bind(':lp_inicio', $inicio)
            ->bind(':lp_fin', $fin)
            ->fetchAll();
    }
}