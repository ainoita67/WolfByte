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

    public function login(string $email, string $password): array
    {
        Validator::validate(compact('email','password'), [
            'email'    => 'required|string|min:3|max:30',
            'password' => 'required|string|min:6|max:100'
        ]);

        try {
            $user = $this->model->findByEmail($email);
        } catch (PDOException $e) {
            throw new \Exception("Error interno en la base de datos", 500);
        }

        if (!$user || $password !== $user['contrasena']) {
            throw new \Exception("Credenciales incorrectas", 401);
        }

        unset($user['contrasena']);
        $user['rol'] = $user['id_rol'];

        // Aseguramos el id_usuario
        $user['id_usuario'] = (int)$user['id_usuario'];

        return $user;
    }
}