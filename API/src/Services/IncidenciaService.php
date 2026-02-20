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
     * Recupera todas las incidencias registradas.
     *
     * Se encarga de solicitar al modelo la lista completa y, si ocurre
     * cualquier fallo en el acceso a la base de datos, convierte el error
     * en una excepción con un mensaje genérico.
     */
    public function getAllIncidencias(): array
    {
        try {
            // Pide al modelo que devuelva todas las incidencias
            return $this->model->all();
        } catch (Throwable $e) {
            // Si algo falla, lo encapsula en una excepción controlada
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    public function getIncidenciasByUsuario(string $id_usuario): array
    {
        try {
            // Pide al modelo que devuelva todas las incidencias de un usuario
            $id=(int)$id_usuario;
            return $this->model->findByUsuario($id);
        } catch (Throwable $e) {
            // Si algo falla, lo encapsula en una excepción controlada
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }
    
    public function getIncidenciasByRecurso(string $id_recurso): array
    {
        try {
            // Pide al modelo que devuelva todas las incidencias de un recurso
            $id=(int)$id_recurso;
            return $this->model->findByRecurso($id);
        } catch (Throwable $e) {
            // Si algo falla, lo encapsula en una excepción controlada
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    // Valida los datos, y tras ello los manda al modelo para crear la incidencia
    public function createIncidencia(array $input): array
    {
        $data = Validator::validate($input, [
            'titulo'        => 'required|string|min:3|max:250',
            'descripcion'   => 'required|string|min:1',
            'fecha'         => 'required|string|min:1',
            'estado'        => 'required|string|min:1',
            'prioridad'     => 'required|string|min:1',
            'id_usuario'    => 'required|int|min:1',
            'id_recurso'    => 'required|string|min:1'
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

        // Valida tanto el ID como los datos a actualizar
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
            // Llama al modelo para actualizar la incidencia
            $result = $this->model->update($id, $data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        // Interpreta el resultado devuelto por el modelo
        if ($result === 0) {
            $exists = $this->model->findById($id);

            if (!$exists) {
                throw new \Exception("Incidencia no encontrada", 404);
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
        // Comprueba que el ID sea válido
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            // Ejecuta la eliminación desde el modelo
            $result = $this->model->delete($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        // Gestiona las posibles respuestas del modelo
        if ($result === 0) {
            // No existe la incidencia
            throw new \Exception("Incidencia no encontrada", 404);
        }

        if ($result === -1) {
            // Conflicto por FK u otra restricción
            throw new \Exception("No se puede eliminar la Incidencia: el registro está en uso", 409);
        }

        // Si llega aquí, la eliminación fue correcta
    }
}
