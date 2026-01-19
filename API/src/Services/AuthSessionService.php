<?php
declare(strict_types=1);

namespace Services;

use Validation\Validator;
use Validation\ValidationException;
use Models\UsuarioModel;
use Core\Session;
use PDOException;

class AuthSessionService
{
    private UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    public function login(string $login, string $password): array
    {
        // Validar datos
        Validator::validate(compact('login','password'), [
            'login'    => 'required|string|min:3|max:30',
            'password' => 'required|string|min:6|max:100'
        ]);

        try {
            $user = $this->model->findByLogin($login);
        } catch (PDOException $e) {
            throw new \Exception("Error interno en la base de datos", 500);
        }

        if (!$user || $password !== $user['contrasena']) {
    throw new \Exception("Credenciales incorrectas", 401);
}


        // Limpiar datos sensibles
        unset($user['contrasena']); 
        $user['rol'] = $user['id_rol'];

        return $user;
    }
}
