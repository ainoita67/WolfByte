<?php
declare(strict_types=1);

namespace Services;

use Models\RolModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class RolService
{
    private RolModel $model;

    public function __construct()
    {
        $this->model = new RolModel();
    }

    /**
     * Obtener todos los roles
     */
    public function getRoles(): array
    {
        try {
            return $this->model->findAll();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }
}