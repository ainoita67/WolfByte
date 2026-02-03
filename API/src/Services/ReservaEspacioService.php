<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaEspacioModel;
use Models\ReservaModel;
use Validation\ValidationException;

class ReservaEspacioService
{
    private ReservaEspacioModel $model;
    private ReservaModel $reservaModel;

    public function __construct()
    {
        $this->model = new ReservaEspacioModel();
        $this->reservaModel = new ReservaModel();
    }

    /**
     * Crear nueva reserva de espacio (flujo completo)
     */
    public function createReservaEspacio(array $data): array
    {
        $errors = [];

        // Validar campos obligatorios para reserva de espacio
        if (empty($data['id_reserva_espacio'])) $errors['id_reserva_espacio'] = 'ID de reserva obligatorio';
        if (empty($data['actividad'])) $errors['actividad'] = 'Actividad obligatoria';
        if (empty($data['id_espacio'])) $errors['id_espacio'] = 'ID de espacio obligatorio';

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        // Crear reserva de espacio usando la FK
        return $this->model->create([
            'id_reserva_espacio' => $reserva['id_reserva'],
            'actividad' => $data['actividad'],
            'id_espacio' => $data['id_espacio']
        ]);
    }
    /**
     * Obtener todas las reservas de espacios
     */
    public function getAllReservas(): array
    {
        return $this->model->getAll();
    }

    /**
     * Obtener reservas de un espacio específico
     */
    public function getReservasByEspacio(int $idEspacio): array
    {
        return $this->model->getByEspacio($idEspacio);
    } 
}
