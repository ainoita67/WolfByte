<?php
declare(strict_types=1);

// Services/IncidenciaService.php

namespace Services;

use Models\IncidenciaModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class IncidenciaService
{
    private IncidenciaModel $model;

    public function __construct()
    {
        $this->model = new IncidenciaModel();
    }


    /**
     * Obtiene todos las incidencias desde la base de datos.
     *
     * Llama al modelo para recuperar la lista completa de incidencias y,
     * en caso de error durante el acceso a la base de datos, lanza una
     * excepción con un mensaje genérico y código HTTP 500.
     */
    public function getAllIncidencias(): array
    {
        try {
            // Solicita al modelo todos los registros de incidencias
            return $this->model->all();
        } catch (Throwable $e) {
            // Captura cualquier error de base de datos y lo transforma en un error interno controlado
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }



    //valida los datos, hashea la contraseña y envia los datos al modelo
    public function createIncidencia(array $input): array
    {
        $data = Validator::validate($input, [
            'titulo'        => 'required|string|min:3|max:250',
            'descripcion'   => 'string',
            'id_ubicacion'  => 'required|int|min:1',
            'id_estado'     => 'required|int|min:1',
            'id_prioridad'  => 'required|int|min:1',
            'id_incidencia'   => 'required|int|min:1'
        ]);

        try {
            $id = $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$id) {
            throw new \Exception("No se pudo crear la Incidencia");
        }

        return ['id' => $id];
    }


    public function updateIncidencia(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);
        //valida input y id
        $data = Validator::validate($input, [
            'titulo'        => 'required|string|min:3|max:250',
            'descripcion'   => 'string',
            'fecha'         => 'required|string|min:1',
            'prioridad'     => 'required|string|min:1',
            'estado'        => 'required|string|min:1',
            'id_usuario'    => 'required|int|min:1',
            'id_recurso'    => 'required|string|min:1'
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
                throw new \Exception("Incidencia no encontrado", 404);
            }

            return [
                'status' => 'no_changes',
                'message' => 'No hubo cambios en los datos de la incidencia'
            ];
        }

        if ($result === -1) {
            throw new \Exception("No se pudo actualizar la incidencia: conflicto con restricciones", 409);
        }

        return [
            'status' => 'updated',
            'message' => 'Incidencia actualizada correctamente'
        ];
    }



    public function deleteIncidencia(int $id): void
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            //ejecuta el delete en el modelo
            $result = $this->model->delete($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        // devuelve los resultados
        if ($result === 0) {
            // No existe el registro
            throw new \Exception("Incidencia no encontrado", 404);
        }

        if ($result === -1) {
            // Conflicto por FK u otra restricción
            throw new \Exception("No se puede eliminar la Incidencia: el registro está en uso", 409);
        }

        // Eliminación exitosa → no retorna nada
    }

    

    
}
