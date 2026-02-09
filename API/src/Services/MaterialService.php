<?php
declare(strict_types=1);

namespace Services;

use Models\MaterialModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class MaterialService
{
    private MaterialModel $model;

    public function __construct()
    {
        $this->model = new MaterialModel();
    }

    public function getAllMaterials(): array
    {
        try {
            return $this->model->getAll();
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener materiales: " . $e->getMessage(), 500);
        }
    }

    public function getMaterialById(string $id): array
    {
        try {
            $material = $this->model->findById($id);
            
            if (!$material) {
                throw new ValidationException(["Material no encontrado"]);
            }

            return $material;
        } catch (Throwable $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            throw new \Exception("Error al obtener material: " . $e->getMessage(), 500);
        }
    }

    public function createMaterial(array $input): array
    {
        // Preparar los datos con valores por defecto
        $data = [
            'id_recurso' => $input['id_recurso'] ?? '',
            'descripcion' => $input['descripcion'] ?? '',
            'unidades' => $input['unidades'] ?? 0,
            'activo' => isset($input['activo']) ? 
                    (filter_var($input['activo'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0) : 1,
            'especial' => isset($input['especial']) ? 
                        (filter_var($input['especial'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0) : 0
        ];

        // Validar datos
        Validator::validate($data, [
            'id_recurso' => 'required|string|min:1|max:10',
            'descripcion' => 'string|max:255',
            'unidades' => 'required|int|min:0',
            'activo' => 'int|in:0,1',  // Cambiado de boolean a int
            'especial' => 'int|in:0,1' // Cambiado de boolean a int
        ]);

        // Validaciones adicionales
        if ($data['unidades'] < 0) {
            throw new ValidationException(["Las unidades no pueden ser negativas"]);
        }

        try {
            return $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error al crear material: " . $e->getMessage(), 500);
        }
    }

    public function updateMaterial(string $id, array $input): array
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        // Preparar y validar datos
        $data = [];
        
        // Validar cada campo si está presente
        if (isset($input['descripcion'])) {
            $data['descripcion'] = (string)$input['descripcion'];
        }
        
        if (isset($input['unidades'])) {
            Validator::validate(['unidades' => $input['unidades']], [
                'unidades' => 'int|min:0'
            ]);
            $data['unidades'] = (int)$input['unidades'];
        }
        
        if (isset($input['activo'])) {
            // Convertir boolean/string a int
            $data['activo'] = filter_var($input['activo'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        
        if (isset($input['especial'])) {
            // Convertir boolean/string a int
            $data['especial'] = filter_var($input['especial'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        // Validaciones adicionales
        if (isset($data['unidades']) && $data['unidades'] < 0) {
            throw new ValidationException(["Las unidades no pueden ser negativas"]);
        }

        try {
            $material = $this->model->update($id, $data);
            
            if (!$material) {
                throw new ValidationException(["Material no encontrado"]);
            }

            return $material;
        } catch (Throwable $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            throw new \Exception("Error al actualizar material: " . $e->getMessage(), 500);
        }
    }

    public function checkAvailability(string $id, string $fecha): array
    {
        // Validar ID y fecha
        Validator::validate([
            'id' => $id,
            'fecha' => $fecha
        ], [
            'id' => 'required|string|min:1|max:10',
            'fecha' => 'required|string'
        ]);

        try {
            return $this->model->checkAvailability($id, $fecha);
        } catch (Throwable $e) {
            throw new \Exception("Error al verificar disponibilidad: " . $e->getMessage(), 500);
        }
    }

    public function searchMaterials(array $filters): array
    {
        // Validar filtros
        $validatedFilters = [];
        
        if (isset($filters['descripcion'])) {
            $validatedFilters['descripcion'] = (string)$filters['descripcion'];
        }
        
        if (isset($filters['activo'])) {
            $validatedFilters['activo'] = filter_var($filters['activo'], FILTER_VALIDATE_BOOLEAN);
        }
        
        if (isset($filters['especial'])) {
            $validatedFilters['especial'] = filter_var($filters['especial'], FILTER_VALIDATE_BOOLEAN);
        }
        
        if (isset($filters['id_recurso'])) {
            $validatedFilters['id_recurso'] = (string)$filters['id_recurso'];
        }

        try {
            return $this->model->search($validatedFilters);
        } catch (Throwable $e) {
            throw new \Exception("Error al buscar materiales: " . $e->getMessage(), 500);
        }
    }

    public function updateStock(string $id, int $unidades): array
    {
        // Validar ID y unidades
        Validator::validate([
            'id' => $id,
            'unidades' => $unidades
        ], [
            'id' => 'required|string|min:1|max:10',
            'unidades' => 'required|int|min:0'
        ]);

        try {
            $success = $this->model->updateStock($id, $unidades);
            
            if (!$success) {
                throw new ValidationException(["No se pudo actualizar el stock"]);
            }

            return $this->model->findById($id);
        } catch (Throwable $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            throw new \Exception("Error al actualizar stock: " . $e->getMessage(), 500);
        }
    }
}