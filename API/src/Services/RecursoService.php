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