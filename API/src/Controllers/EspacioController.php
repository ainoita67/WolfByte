<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Models\EspacioModel;

class EspacioController
{
    private EspacioModel $model;

    public function __construct()
    {
        $this->model = new EspacioModel();
    }

    /**
     * GET /espacios
     * Listar todos los espacios
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $espacios = $this->model->getAll();
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /espacios/{id}
     * Mostrar un espacio específico
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $espacio = $this->model->findById($id);
            
            if (!$espacio) {
                throw new \Exception("Espacio no encontrado", 404);
            }
            
            $res->status(200)->json($espacio);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * POST /espacios
     * Crear un nuevo espacio
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->json();
            
            // Validaciones básicas
            $errors = $this->validarDatos($data);
            if (!empty($errors)) {
                throw new ValidationException($errors, 422);
            }
            
            // Verificar si ya existe un espacio con ese ID
            $existente = $this->model->findById($data['id_recurso']);
            if ($existente) {
                throw new \Exception("Ya existe un espacio con el ID: " . $data['id_recurso'], 409);
            }
            
            $resultado = $this->model->create($data);
            
            if (!$resultado) {
                throw new \Exception("Error al crear el espacio", 500);
            }
            
            $res->status(201)->json(
                ['id' => $data['id_recurso']],
                "Espacio creado correctamente"
            );
            
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * PUT /espacios/{id}
     * Actualizar un espacio existente
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json();
            
            // Validaciones básicas
            $errors = $this->validarDatos($data, true);
            if (!empty($errors)) {
                throw new ValidationException($errors, 422);
            }
            
            // Verificar que el espacio existe
            $existente = $this->model->findById($id);
            if (!$existente) {
                throw new \Exception("Espacio no encontrado", 404);
            }
            
            // Asegurar que el ID en la URL coincide con el del body
            $data['id_recurso'] = $id;
            
            $resultado = $this->model->update($id, $data);
            
            if ($resultado === 0) {
                $res->status(200)->json(['status' => 'no_changes', 'message' => 'No se realizaron cambios']);
                return;
            }
            
            if ($resultado === -1) {
                throw new \Exception("Error al actualizar el espacio", 409);
            }
            
            $res->status(200)->json(
                ['id' => $id],
                "Espacio actualizado correctamente"
            );
            
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * DELETE /espacios/{id}
     * Eliminar un espacio
     */
    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            // Verificar que el espacio existe
            $existente = $this->model->findById($id);
            if (!$existente) {
                throw new \Exception("Espacio no encontrado", 404);
            }
            
            $resultado = $this->model->delete($id);
            
            if ($resultado === 0) {
                throw new \Exception("Espacio no encontrado", 404);
            }
            
            if ($resultado === -1) {
                throw new \Exception("No se puede eliminar el espacio porque está siendo utilizado", 409);
            }
            
            $res->status(200)->json([], "Espacio eliminado correctamente");
            
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * GET /espacios/aulas
     * Listar solo aulas
     */
    public function getAulas(Request $req, Response $res): void
    {
        try {
            $aulas = $this->model->getAllAulas();
            $res->status(200)->json($aulas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /espacios/otros
     * Listar otros espacios (no aulas)
     */
    public function getOtrosEspacios(Request $req, Response $res): void
    {
        try {
            $otros = $this->model->getOtrosEspacios();
            $res->status(200)->json($otros);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Validar datos del espacio
     */
    private function validarDatos(array $data, bool $isUpdate = false): array
    {
        $errors = [];
        
        if (!isset($data['id_recurso']) || empty(trim($data['id_recurso']))) {
            $errors['id_recurso'] = 'El ID del espacio es requerido';
        } elseif (!$isUpdate && strlen($data['id_recurso']) > 10) {
            $errors['id_recurso'] = 'El ID no puede exceder los 10 caracteres';
        }
        
        if (!isset($data['descripcion']) || empty(trim($data['descripcion']))) {
            $errors['descripcion'] = 'La descripción es requerida';
        }
        
        if (!isset($data['id_edificio']) || empty($data['id_edificio'])) {
            $errors['id_edificio'] = 'El edificio es requerido';
        } elseif (!is_numeric($data['id_edificio'])) {
            $errors['id_edificio'] = 'El edificio debe ser un número válido';
        }
        
        if (!isset($data['numero_planta']) && $data['numero_planta'] !== '0') {
            $errors['numero_planta'] = 'La planta es requerida';
        } elseif (!is_numeric($data['numero_planta'])) {
            $errors['numero_planta'] = 'La planta debe ser un número válido';
        }
        
        // Valores por defecto
        if (!isset($data['activo'])) {
            $data['activo'] = 1;
        }
        
        if (!isset($data['especial'])) {
            $data['especial'] = 0;
        }
        
        if (!isset($data['es_aula'])) {
            $errors['es_aula'] = 'El tipo de espacio es requerido';
        }
        
        return $errors;
    }
}