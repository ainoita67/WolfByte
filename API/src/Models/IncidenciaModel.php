<?php
declare(strict_types=1);

// Models/IncidenciaModel.php

namespace Models;

use Core\DB;

class IncidenciaModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Obtiene todos los incidencias almacenados en la base de datos y devuelve el 
     * resultado como un array asociativo.
     */
    public function all(): array
    {
        return $this->db
            ->query("SELECT * FROM Incidencia")
            ->fetchAll();
    }


    //consulta a la bdd devuelve el id si se ha insertado o false en caso de error
    public function create(array $data): int|false
    {
        $this->db->query("
            INSERT INTO Incidencia
            (titulo, descripcion, id_ubicacion, id_estado, id_prioridad, id_profesor) 
            VALUES (:titulo, :descripcion, :id_ubicacion, :id_estado, :id_prioridad, :id_profesor)
        ")
        ->bind(":titulo",  $data['titulo'])
        ->bind(":descripcion", $data['descripcion'])
        ->bind(":id_ubicacion",   $data['id_ubicacion'])
        ->bind(":id_estado",    $data['id_estado'])
        ->bind(":id_prioridad",      $data['id_prioridad'])
        ->bind(":id_profesor",      $data['id_profesor'])
        ->execute();

        return (int) $this->db->lastId();
    }

    public function update(int $id, array $data): int
    {
        $this->db->query("
            UPDATE Incidencia SET
                titulo = :titulo,
                descripcion = :descripcion,
                id_ubicacion = :id_ubicacion,
                id_estado = :id_estado,
                id_prioridad = :id_prioridad,
                id_profesor = :id_profesor,
                updated_at = NOW()
            WHERE id_incidencia = :id
        ")
        ->bind(":id", $id)
        ->bind(":titulo",  $data['titulo'])
        ->bind(":descripcion", $data['descripcion'])
        ->bind(":id_ubicacion",   $data['id_ubicacion'])
        ->bind(":id_estado",    $data['id_estado'])
        ->bind(":id_prioridad",      $data['id_prioridad'])
        ->bind(":id_profesor",      $data['id_profesor'])
        ->execute();

        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'];
    }

 
    public function delete(int $id): int
    //elimina con parametros y devuelve el numero de filas eliminadas
    {
        $this->db->query("DELETE FROM Incidencia WHERE id_incidencia = :id")
                 ->bind(":id", $id)
                 ->execute();

        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'];
    }
}
