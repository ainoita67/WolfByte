<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaModel;
use Validation\ValidationException;

class ReservaService
{
    private ReservaModel $model;

    public function __construct()
    {
        $this->model = new ReservaModel();
    }

    public function getAllReservas(): array
    {
        return $this->model->getAll();
    }

    public function getReservasUsuario(int $idUsuario): array
    {
        if ($idUsuario <= 0) {
            throw new ValidationException("Usuario no válido");
        }

        return $this->model->getByUsuario($idUsuario);
    }

    public function getReservaById(int $id): array
    {
        $reserva = $this->model->findById($id);
        if (!$reserva) {
            throw new ValidationException("Reserva no encontrada");
        }
        return $reserva;
    }

    public function createReserva(array $data): array
    {
        $this->validateReservaData($data);
        return $this->model->create($data);
    }

    public function updateReserva(int $id, array $data): array
    {
        $this->getReservaById($id);
        $this->validateReservaData($data, false);
        return $this->model->update($id, $data);
    }

    public function deleteReserva(int $id): void
    {
        $this->getReservaById($id);
        $this->model->delete($id);
    }

    private function validateReservaData(array $data, bool $isNew = true): void
    {
        if ($isNew && empty($data['id_usuario'])) {
            throw new ValidationException("El usuario es obligatorio");
        }

        if (empty($data['inicio'])) {
            throw new ValidationException("La fecha de inicio es obligatoria");
        }

        if (empty($data['fin'])) {
            throw new ValidationException("La fecha de fin es obligatoria");
        }

        if (strtotime($data['fin']) <= strtotime($data['inicio'])) {
            throw new ValidationException("La fecha de fin debe ser posterior a la de inicio");
        }
    }
}
