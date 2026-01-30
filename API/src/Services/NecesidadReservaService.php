<?php
declare(strict_types=1);

namespace Services;

use Models\NecesidadReservaModel;
use Validation\ValidationException;

class NecesidadReservaService
{
    private NecesidadReservaModel $model;

    public function __construct()
    {
        $this->model = new NecesidadReservaModel();
    }

    public function getAllNecesidades(): array
    {
        return $this->model->getAll();
    }

    public function getNecesidadById(int $id): array
    {
        $necesidad = $this->model->findById($id);

        if (!$necesidad) {
            throw new ValidationException("Necesidad de reserva no encontrada");
        }

        return $necesidad;
    }

    public function createNecesidad(array $data): array
    {
        if (empty($data['id_reserva_espacio']) || empty($data['id_necesidad'])) {
            throw new ValidationException("id_reserva_espacio y id_necesidad son obligatorios");
        }

        return $this->model->create($data);
    }

    public function updateNecesidad(int $id, array $data): array
    {
        if (empty($data['id_necesidad'])) {
            throw new ValidationException("id_necesidad es obligatorio");
        }

        return $this->model->update($id, $data);
    }

    public function deleteNecesidad(int $id): void
    {
        $this->model->delete($id);
    }
}
