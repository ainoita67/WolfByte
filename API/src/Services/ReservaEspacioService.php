<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaEspacioModel;
use Services\ReservaService;
use Validation\ValidationException;
use Validation\Validator;

class ReservaEspacioService
{
    private ReservaEspacioModel $model;
    private ReservaService $serviceReserva;

    public function __construct()
    {
        $this->model = new ReservaEspacioModel();
        $this->serviceReserva = new ReservaService();
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
                throw new \Exception("El campo {$field} es obligatorio");
            }
        }

        $inicio = date("Y-m-d H:i:s", strtotime($data['inicio']));
        $fin = date("Y-m-d H:i:s", strtotime($data['fin']));
        $creacion = date("Y-m-d H:i:s", strtotime($data['f_creacion']));

        if ($inicio >= $fin) {
            throw new \Exception("La fecha de inicio debe ser anterior a la fecha de fin");
        }

        if($creacion > $inicio){
            throw new \Exception("La fecha de creación no puede ser posterior a la fecha de inicio");
        }

        if(!$this->model->getReservaFecha(0, $data)){
            throw new \Exception("Ya hay una reserva entre esas horas");
        }

        $reserva = $this->serviceReserva->createReserva($data);
        if(!$reserva['id_reserva']||$reserva['id_reserva']<=0){
            throw new \Exception("Error al crear la reserva");
        }
        
        $this->model->create($reserva['id_reserva'], $data);

        return [
            'id_reserva' => $reserva['id_reserva'],
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