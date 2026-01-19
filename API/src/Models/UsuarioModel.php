<?php
declare(strict_types=1);

namespace Models;

use Core\DB;

class UsuarioModel
{
    private DB $db;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function findByLogin(string $login): array|false
    {
        return $this->db
            ->query("SELECT * FROM Usuario WHERE nombre = :login")
            ->bind(':login', $login)
            ->fetch();
    }

    // Otros métodos CRUD si los necesitas
}
