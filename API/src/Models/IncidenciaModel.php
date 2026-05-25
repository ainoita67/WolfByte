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


    /**
     * Obtener todas las incidencias por páginas
     */
    public function allPaginadas(array $data=[]): array
    {
        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? 25;
        $offset = ($page - 1) * $perPage;

        return $this->db
            ->query("SELECT * FROM Incidencia i
                ORDER BY i.id_incidencia DESC
                LIMIT :inicio, :fin
            ")
            ->bind(':inicio', $offset)
            ->bind(':fin', $perPage)
            ->fetchAll();
    }


    public function totalpaginas(array $data=[]): int
    {
        $perPage = $data['perPage'] ?? null;
        $totalRows = $this->db->query("SELECT COUNT(*) AS total FROM Incidencia")->fetch()['total'];

        if($perPage!==null){
            $totalPages = ceil($totalRows / $perPage);
            return (int)$totalPages;
        }else{
            return $totalRows;
        }
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
    //Elimina con parámetros y devuelve el número de filas eliminadas
    {
        $this->db->query("DELETE FROM Incidencia WHERE id_incidencia = :id")
                 ->bind(":id", $id)
                 ->execute();

        return $this->db->query("SELECT ROW_COUNT() AS affected")->fetch()['affected'];
    }
}