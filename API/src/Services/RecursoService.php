<?php
declare(strict_types=1);

namespace Services;

use Core\Request;
use Core\Response;
use Models\RecursoModel;
use Throwable;
use Validation\Validator;
use Validation\ValidationException;

class RecursoService
{
    private RecursoModel $model;

    public function __construct()
    {
        $this->model = new RecursoModel();
    }

    /**
     * Obtener todos recursos
     */
    public function getAllRecursos(): array
    {
        return $this->model->getAll();
    }

    /**
     * Obtener todos recursos activos
     */
    public function getAllRecursosActivos(): array
    {
        return $this->model->getAllActivos();
    }

    /**
     * Obtener recurso por ID
     */
    public function getRecursoById(string $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1'
        ]);

        try {
            $recurso = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$recurso) {
            throw new \Exception("Recurso no encontrado", 404);
        }

        return $recurso;
    }

    /**
     * Cambiar estado de activo de un recurso
     */
    public function activoRecurso(string $id): array
    {
        $data = Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1'
        ]);

        if(empty($data['id'])) {
            throw new ValidationException("id es obligatorio");
        }

        if($this->model->findById($data['id'])['activo'] === 1) {
            return $this->model->desactivo($data['id']);
        }else{
            return $this->model->activo($data['id']);
        }
    }
}