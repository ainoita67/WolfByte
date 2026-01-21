<?php
declare(strict_types=1);

namespace Services;

use Models\EdificioModel;
use Validation\ValidationException;

class EdificioService
{
    private EdificioModel $model;

    public function __construct()
    {
        $this->model = new EdificioModel();
    }

    public function getAllEdificios(): array
    {
        return $this->model->getAll();
    }

    public function getEdificioById(int $id): array
    {
        $edificio = $this->model->findById($id);

        if (!$edificio) {
            throw new ValidationException("Edificio no encontrado");
        }

        return $edificio;
    }

    public function createEdificio(array $data): array
    {
        if (empty($data['nombre_edificio'])) {
            throw new ValidationException("El nombre del edificio es obligatorio");
        }

        return $this->model->create($data);
    }

    public function updateEdificio(int $id, array $data): array
    {
        if (empty($data['nombre_edificio'])) {
            throw new ValidationException("El nombre del edificio es obligatorio");
        }

        return $this->model->update($id, $data);
    }

    public function deleteEdificio(int $id): void
    {
        $this->model->delete($id);
    }
}
