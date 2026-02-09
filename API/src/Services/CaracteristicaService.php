<?php
declare(strict_types=1);

namespace Services;

use Models\CaracteristicaModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class CaracteristicaService
{
    private CaracteristicaModel $model;

    public function __construct()
    {
        $this->model = new CaracteristicaModel();
    }

    public function getAllCaracteristicas(): array
    {
        try {
            // Solicita al modelo todos los registros de Caracteristica
            return $this->model->getAll();
        } catch (Throwable $e) {
            // Captura cualquier error de base de datos y lo transforma en un error interno controlado
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    public function getCaracteristicaById(int $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            $caracteristica = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$caracteristica) {
            throw new \Exception("Profesor no encontrado", 404);
        }

        return $caracteristica;
    }

    public function createCaracteristica(array $input): array
    {
        $data = Validator::validate($input, [
            'nombre' => 'string',
        ]);

        try {
            $id = $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$id) {
            throw new \Exception("No se pudo crear la caracteristica");
        }

        return ['id' => $id];
    }

    public function updateCaracteristica(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);
        //valida input y id
        $data = Validator::validate($input, [
            'nombre'   => 'string',
        ]);

        try {
            //llama al modelo
            $result = $this->model->update($id, $data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        //usa lo recibido del modelo para dar los mensajes de success o de error
        if ($result === 0) {
            $exists = $this->model->findById($id);

            if (!$exists) {
                throw new \Exception("Profesor no encontrado", 404);
            }

            return [
                'status' => 'no_changes',
                'message' => 'No hubo cambios en los datos del profesor'
            ];
        }

        if ($result === -1) {
            throw new \Exception("No se pudo actualizar el profesor: conflicto con restricciones", 409);
        }

        return [
            'status' => 'updated',
            'message' => 'Profesor actualizado correctamente'
        ];
    }

}
