<?php
declare(strict_types=1);

namespace Services;

use Models\NecesidadModel;
use Throwable;
use Validation\Validator;
use Validation\ValidationException;

class NecesidadService
{
    private NecesidadModel $model;

    public function __construct()
    {
        $this->model = new NecesidadModel();
    }

    public function getAllNecesidades(): array
    {
        return $this->model->getAll();
    }

    public function createNecesidad(array $data): array
    {
        if (empty($data['nombre'])) {
            throw new ValidationException("El nombre de la necesidad es obligatorio");
        }

        $data['nombre'] = Validator::capitalizar($data['nombre']);

        return $this->model->create($data);
    }

    public function updateNecesidad(int $id, array $data): array
    {
        if (empty($data['nombre'])) {
            throw new ValidationException("El nombre de la necesidad es obligatorio");
        }

        return $this->model->update($id, Validator::capitalizar($data['nombre']));
    }
}