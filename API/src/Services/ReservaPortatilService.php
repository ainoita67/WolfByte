<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaPortatilModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class ReservaPortatilService
{
    private ReservaPortatilModel $model;

    public function __construct()
    {
        $this->model = new ReservaPortatilModel();
    }

    // Devuelve todas las reservas de portatils
    public function getAllReservas(): array
    {
        return $this->model->getAll();
    }

    // Devuelve todas las reservas de un portatil específico
    public function getReservasPorPortatil(string $idPortatil): array
    {
        Validator::validate(['id' => $idPortatil], [
            'id' => 'required|string|min:1'
        ]);

        try {
            $reservas = $this->model->getByPortatil($idPortatil);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$reservas) {
            throw new \Exception("reservas no encontrado", 404);
        }

        return $reservas;
    }

    // Devuelve una reserva por su ID
    public function getReservaById(int $id): array
    {
        $reserva = $this->model->findById($id);

        if (!$reserva) {
            throw new ValidationException([
                "Reserva ".$id => "Reserva no encontrada"
            ]);
        }

        return $reserva;
    }

    // Crea una nueva reserva
    public function createReserva(array $data): array
    {
        $this->validateReservaData($data);
        return $this->model->create($data);
    }

    // Actualiza una reserva existente
    public function updateReserva(int $id, array $data): array
    {
        $this->getReservaById($id);
        $this->validateReservaData($data, false);
        return $this->model->update($id, $data);
    }

    // Valida los datos de la reserva
    private function validateReservaData(array $data, bool $isNew = true): void
    {
        $errors = [];

        if (empty($data['unidades'])) {
            $errors['unidades'] = "Las unidades son obligatorias";
        }

        if (empty($data['usaenespacio'])) {
            $errors['usaenespacio'] = "El espacio de uso es obligatorio";
        }

        if (!isset($data['id_material']) || is_numeric($data['id_material'])) {
            $errors['id_material'] = "El ID del portátil es obligatorio y debe ser texto";
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
