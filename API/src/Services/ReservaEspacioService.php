<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaEspacioModel;
use Validation\ValidationException;

class ReservaEspacioService
{
    private ReservaEspacioModel $model;

    public function __construct()
    {
        $this->model = new ReservaEspacioModel();
    }

    /**
     * Obtener todas las reservas de un espacio
     */
    public function getReservasPorEspacio(string $idEspacio): array
{
    if (!$idEspacio) {
        throw new ValidationException("ID de espacio no válido");
    }

    return $this->model->getReservasPorEspacio($idEspacio);
}

}
