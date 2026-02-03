<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaEspacio;
use Validation\ValidationException;

class ReservaEspacioService
{
    private ReservaEspacio $model;

    public function __construct()
    {
        $this->model = new ReservaEspacio();
    }

    public function createReservaEspacio(array $data): array
    {
        $this->validate($data);
        return $this->model->create($data); // Devuelve el array correctamente
    }


    public function getAllReservas(): array
    {
        return $this->model->getAll();
    }

    public function getReservasByEspacio(string $idEspacio): array
    {
        return $this->model->getByEspacio($idEspacio);
    }

    private function validate(array $data): void
    {
        $required = ['asignatura', 'grupo', 'profesor', 'inicio', 'fin', 'id_usuario', 'id_espacio'];

        $errors = [];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "El campo $field es obligatorio.";
            }
        }

        if (!empty($data['inicio']) && !empty($data['fin'])) {
            if (strtotime($data['inicio']) >= strtotime($data['fin'])) {
                $errors[] = "La fecha de inicio debe ser menor que la fecha de fin.";
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

}
