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

    /**
     * Reservas del usuario
     */
    public function getReservasUsuario(int $idUsuario): array
    {
        if ($idUsuario <= 0) {
            throw new ValidationException("Usuario no válido");
        }

        return $this->model->getByUsuario($idUsuario);
    }

    /**
     * Reserva por ID
     */
    public function getReservaById(int $id): array
    {
        $reserva = $this->model->findById($id);

        if (!$reserva) {
            throw new ValidationException("Reserva no encontrada");
        }

        return $reserva;
    }
}
