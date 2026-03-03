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
            return $this->model->getAll();
        } catch (Throwable $e) {
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
            throw new \Exception("Caracteristica no encontrada", 404);
        }

        return $caracteristica;
    }

    public function createCaracteristica(array $input): array
    {
        $data = Validator::validate($input, [
            'nombre' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras, números y espacios'
        ]);

        try {
            $id = $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$id) {
            throw new \Exception("Ya existe una característica con ese nombre", 409);
        }

        return ['id' => $id];
    }

    public function updateCaracteristica(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'nombre' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras, números y espacios'
        ]);

        try {
            $result = $this->model->update($id, $data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if ($result === 0) {
            $exists = $this->model->findById($id);

            if (!$exists) {
                throw new \Exception("Caracteristica no encontrada", 404);
            }

            return [
                'status' => 'no_changes',
                'message' => 'No hubo cambios en los datos de la caracteristica'
            ];
        }

        if ($result === -1) {
            throw new \Exception("Ya existe otra característica con ese nombre", 409);
        }

        return [
            'status' => 'updated',
            'message' => 'Caracteristica actualizada correctamente'
        ];
    }

    public function deleteCaracteristica(int $id): void
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            $result = $this->model->delete($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if ($result === 0) {
            throw new \Exception("Caracteristica no encontrada", 404);
        }

        if ($result === -1) {
            throw new \Exception("No se puede eliminar la caracteristica porque está siendo utilizada por uno o más espacios", 409);
        }
    }

    /**
     * Obtener características de un espacio
     */
    public function getCaracteristicasByEspacio(string $idEspacio): array
    {
        Validator::validate(['id_espacio' => $idEspacio], [
            'id_espacio' => 'required|string|min:1|max:10'
        ]);

        try {
            return $this->model->getByEspacio($idEspacio);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Asignar característica a espacio
     */
    public function asignarCaracteristicaAEspacio(string $idEspacio, int $idCaracteristica): array
    {
        Validator::validate([
            'id_espacio' => $idEspacio,
            'id_caracteristica' => $idCaracteristica
        ], [
            'id_espacio' => 'required|string|min:1|max:10',
            'id_caracteristica' => 'required|int|min:1'
        ]);

        try {
            // Verificar que la característica existe
            $caracteristica = $this->model->findById($idCaracteristica);
            if (!$caracteristica) {
                throw new \Exception("Característica no encontrada", 404);
            }

            $resultado = $this->model->asignarAEspacio($idEspacio, $idCaracteristica);

            if (!$resultado) {
                throw new \Exception("La característica ya está asignada a este espacio", 409);
            }

            return $caracteristica;
        } catch (\Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }
}