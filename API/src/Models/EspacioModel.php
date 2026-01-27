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
        
    }

    


}