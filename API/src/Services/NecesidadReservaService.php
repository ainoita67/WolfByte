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
        if (empty($data['id_reserva_espacio']) || empty($data['necesidades'])) {
            throw new ValidationException("id_reserva_espacio y id_necesidad son obligatorios");
        }
        $resultados=[];
        foreach ($data['necesidades'] as $n) {
            if (!isset($n['id_necesidad'])) {
                throw new ValidationException("Cada necesidad debe tener un id_necesidad");
            }
        }
        foreach ($data['necesidades'] as $necesidad) {
            $insertData = [
                'id_reserva_espacio' => $data['id_reserva_espacio'],
                'id_necesidad' => $necesidad['id_necesidad']
            ];
            $resultados = $this->model->create($insertData);
        }

        return $resultados;
    }

    public function updateNecesidad(int $id, array $input): array
    {
        $resultados=[];
        if($input==null){
            $this->model->delete($id, $input);
        }else{
            foreach ($input['necesidades'] as $n) {
                if (!isset($n['id_necesidad'])) {
                    throw new ValidationException("Cada necesidad debe tener un id_necesidad");
                }
            }
            $this->model->delete($id, $input);
            foreach ($input['necesidades'] as $necesidad) {
                $data = [
                    'id_reserva_espacio' => (int)$id,
                    'id_necesidad' => (int)$necesidad['id_necesidad']
                ];
                $resultados = $this->model->create($data);
            }
        }
        return $resultados;
    }

    public function deleteNecesidad(int $id): void
    {
        $this->model->delete($id);
    }
}
