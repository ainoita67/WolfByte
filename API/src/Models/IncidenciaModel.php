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


    public function findById(int $id): array|false{
        return $this->db
            ->query("SELECT * FROM Incidencia WHERE id_incidencia=:id")
            ->bind(":id", $id)
            ->fetchAll();
    }


    public function findByUsuario(int $id_usuario): array|false{
        return $this->db
            ->query("SELECT * FROM Incidencia WHERE id_usuario=:id_usuario")
            ->bind(":id_usuario", $id_usuario)
            ->fetchAll();
    }


    public function findByRecurso(int $id_recurso): array|false{
        return $this->db
            ->query("SELECT * FROM Incidencia WHERE id_recurso=:id_recurso")
            ->bind(":id_recurso", $id_recurso)
            ->fetchAll();
    }


    //consulta a la bdd devuelve el id si se ha insertado o false en caso de error
    public function create(array $data): int|false
    {
        $this->db->query("
            INSERT INTO Incidencia
            (titulo, descripcion, fecha, estado, prioridad, id_usuario, id_recurso) 
            VALUES (:titulo, :descripcion, :fecha, :estado, :prioridad, :id_usuario, :id_recurso)
        ")
        ->bind(":titulo",           $data['titulo'])
        ->bind(":descripcion",      $data['descripcion'])
        ->bind(":fecha",            $data['fecha'])
        ->bind(":estado",           $data['estado'])
        ->bind(":prioridad",        $data['prioridad'])
        ->bind(":id_usuario",       $data['id_usuario'])
        ->bind(":id_recurso",       $data['id_recurso'])
        ->execute();

        return (int) $this->db->lastId();
    }

    public function update(int $id, array $data): int
    {
        $this->db->query("
            UPDATE Incidencia SET
                titulo = :titulo,
                descripcion = :descripcion,
                fecha = :fecha,
                estado = :estado,
                prioridad = :prioridad,
                id_usuario = :id_usuario
            WHERE id_incidencia = :id
        ")
        ->bind(":id", $id)
        ->bind(":titulo",           $data['titulo'])
        ->bind(":descripcion",      $data['descripcion'])
        ->bind(":fecha",            $data['fecha'])
        ->bind(":estado",           $data['estado'])
        ->bind(":prioridad",        $data['prioridad'])
        ->bind(":id_usuario",      $data['id_usuario'])
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
