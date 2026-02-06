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
                            e.nombre_edificio,
                            es.es_aula
                        FROM Recurso r
                        LEFT JOIN Edificio e ON r.id_edificio = e.id_edificio
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

}