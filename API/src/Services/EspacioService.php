<?php
declare(strict_types=1);

namespace Services;

use Models\EspacioModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class EspacioService
{
    private EspacioModel $model;

    public function __construct()
    {
        $this->model = new EspacioModel();
    }

    /**
     * Obtener todos los espacios
     */
    public function getAllEspacios(): array
    {
        try {
            return $this->model->getAll();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener espacio por ID
     */
    public function getEspacioById(string $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        try {
            $espacio = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$espacio) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        return $espacio;
    }

    /**
     * Crear un nuevo espacio
     */
    public function createEspacio(array $input): array
    {
        //var_dump($input);
        try {
            $data = Validator::validate($input, [
                'id_espacio' => 'required|string|min:1|max:10',
                'descripcion' => 'string|max:255',
                'es_aula' => 'required|int',
                'activo' => 'required|int',
                'especial' => 'required|int',
                'numero_planta' => 'int|min:1|max:5',
                'id_edificio' => 'int|min:1',
                'nombre_edificio' => 'string|min:1|max:100',
                'caracteristicasId' => ''
            ]);

            var_dump($data);
        } catch (ValidationException $e) {
            // Relanzar con formato más amigable o simplemente relanzar
            throw new \Exception("Error de validación: ", 400);
        }

        // Validaciones adicionales
        if (empty($data['id_edificio']) && empty($data['nombre_edificio'])) {
            throw new \Exception("Debe proporcionar id_edificio o nombre_edificio", 400);
        }

        try {
            $id = $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        return ['id' => $id];
    }

    /**
     * Actualizar espacio
     */
    public function updateEspacio(string $id, array $input): array
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        // Validar datos de entrada
        $data = Validator::validate($input, [
            'descripcion' => 'string|max:255',
            'es_aula' => 'boolean',
            'activo' => 'boolean',
            'especial' => 'boolean',
            'numero_planta' => 'int|min:-5|max:50',
            'id_edificio' => 'int|min:1',
            'nombre_edificio' => 'string|min:3|max:100',
            'caracteristicas' => 'array',
            'caracteristicas.*' => 'int|min:1'
        ]);

        // Verificar que el espacio exista antes de actualizar
        try {
            $exists = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$exists) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        try {
            $result = $this->model->update($id, $data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if ($result === 0) {
            return [
                'status' => 'no_changes',
                'message' => 'No hubo cambios en los datos del espacio'
            ];
        }

        return [
            'status' => 'updated',
            'message' => 'Espacio actualizado correctamente',
            'affected_rows' => $result
        ];
    }

    /**
     * Eliminar espacio
     */
    public function deleteEspacio(string $id): void
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        try {
            // Verificar que el espacio exista
            $exists = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$exists) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        try {
            $result = $this->model->delete($id);
        } catch (Throwable $e) {
            // Verificar si es error de restricción de clave foránea
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                throw new \Exception("No se puede eliminar el espacio: está siendo utilizado en otras tablas", 409);
            }
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if ($result === 0) {
            throw new \Exception("No se pudo eliminar el espacio", 500);
        }

        // Eliminación exitosa → no retorna nada
    }

    /**
     * Obtener espacios por edificio
     */
    public function getEspaciosByEdificio(int $idEdificio): array
    {
        Validator::validate(['id_edificio' => $idEdificio], [
            'id_edificio' => 'required|int|min:1'
        ]);

        try {
            return $this->model->getByEdificio($idEdificio);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener espacios activos
     */
    public function getEspaciosActivos(): array
    {
        try {
            return $this->model->getActivos();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Cambiar estado activo/inactivo de un espacio
     */
    public function toggleEstadoEspacio(string $id, int $estado): array
    {
        Validator::validate([
            'id' => $id,
            'estado' => $estado
        ], [
            'id' => 'required|string|min:1|max:10',
            'estado' => 'required|int|in:0,1'
        ]);

        try {
            // Verificar que el espacio exista
            $exists = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$exists) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        try {
            $success = $this->model->toggleEstado($id, $estado);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$success) {
            throw new \Exception("No se pudo cambiar el estado del espacio", 500);
        }

        $estadoTexto = $estado === 1 ? 'activado' : 'desactivado';
        return [
            'status' => 'updated',
            'message' => "Espacio {$estadoTexto} correctamente"
        ];
    }

    /**
     * Obtener características de un espacio específico
     */
    public function getCaracteristicasEspacio(string $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        try {
            // Verificar que el espacio exista
            $exists = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$exists) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        try {
            return $this->model->getCaracteristicas($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Buscar espacios por características
     */
    public function searchEspaciosByCaracteristicas(array $input): array
    {
        $data = Validator::validate($input, [
            'caracteristicas' => 'required|array|min:1',
            'caracteristicas.*' => 'int|min:1'
        ]);

        try {
            // Nota: Necesitarías descomentar el método searchByCaracteristicas en el modelo
            // return $this->model->searchByCaracteristicas($data['caracteristicas']);

            // Mientras tanto, podemos hacer una búsqueda básica filtrando en PHP
            $allEspacios = $this->model->getAll();

            $resultados = [];
            foreach ($allEspacios as $espacio) {
                if (isset($espacio['caracteristicas_ids'])) {
                    $ids = explode(',', $espacio['caracteristicas_ids']);
                    $ids = array_map('intval', $ids);

                    // Verificar si el espacio tiene todas las características solicitadas
                    $intersection = array_intersect($data['caracteristicas'], $ids);
                    if (count($intersection) === count($data['caracteristicas'])) {
                        $resultados[] = $espacio;
                    }
                }
            }

            return $resultados;

        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }
}