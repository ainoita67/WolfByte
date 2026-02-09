<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

class RolModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }



    /**
     * Obtener todos los roles
     */
    public function findAll(): array
    {
        return $this->db
            ->query("SELECT id_rol, rol FROM Rol")
            ->fetchAll();
    }
}