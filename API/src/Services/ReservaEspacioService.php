<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaEspacioModel;
use Validation\ValidationException;
use Validation\Validator;

class ReservaEspacioService
{
    private ReservaEspacioModel $model;

    public function __construct()
    {
        $this->model = new ReservaEspacioModel();
    }

    public function getReservasPorEspacio(string $id): array
    {
        return $this->model->getByEspacio($id);
    }

    public function getReservaById(int $id): array
    {
        $reserva = $this->model->getById($id);

        if (!$reserva) {
            throw new \Exception("Reserva no encontrada");
        }

        return $reserva;
    }

    public function createReserva(array $data): array
    {
        $required = ['asignatura','grupo','profesor','inicio','fin','id_usuario','actividad','id_espacio'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new ValidationException("El campo {$field} es obligatorio");
            }
        }

        // 1️⃣ Crear reserva
        $idReserva = $this->model->createReserva($data);

        // 2️⃣ Crear relación con espacio
        $this->model->createReservaEspacio(
            $idReserva,
            $data['actividad'],
            $data['id_espacio']
        );

        // 3️⃣ Devolver objeto completo (NO el int)
        return [
            'id_reserva' => $idReserva,
            'message' => 'Reserva creada correctamente'
        ];
    }

    public function updateReserva(int $id, array $input): array
    {
        $data = Validator::validate($input, [
            'id_espacio'            => 'required|string|min:1',
            'actividad'             => 'nullable|string|min:1',
            'necesidades'           => 'required',
            'inicio'                => 'required|string|min:1',
            'fin'                   => 'required|string|min:1'
        ]);

        if($this->model->getReservaFecha($id, $data)&&count($this->model->findById($id))>0){
            return $this->model->update($id, $data);
        }
        throw new \Exception("Ya hay una reserva entre esas horas");
    }
}