<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\MaterialService;
use Throwable;
use Validation\ValidationException;

class MaterialController
{
    private MaterialService $service;

    public function __construct()
    {
        $this->service = new MaterialService();
    }

    /**
     * GET /material
     * Obtiene todos los materiales
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $materials = $this->service->getAllMaterials();
            $res->status(200)->json($materials);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /material/{id}
     * Obtiene un material por ID
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $material = $this->service->getMaterialById($id);
            $res->status(200)->json($material);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /material
     * Crea un nuevo material
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $material = $this->service->createMaterial($data);
            $res->status(201)->json($material, "Material creado exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PATCH /material/{id}
     * Actualiza un material existente
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $material = $this->service->updateMaterial($id, $data);
            $res->status(200)->json($material, "Material actualizado exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // En src/Controllers/MaterialController.php - método disponibilidad()
    public function disponibilidad(Request $req, Response $res, string $id): void
    {
        try {
            // Obtener el parámetro 'fecha' de los query params
            $fecha = $req->getParam('fecha'); // o $req->getQueryParams()['fecha']
            
            if (!$fecha) {
                $res->errorJson(["El parámetro 'fecha' es requerido"], 400);
                return;
            }

            $disponibilidad = $this->service->checkAvailability($id, $fecha);
            $res->status(200)->json($disponibilidad);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /material/search
     * Busca materiales con filtros
     */
    public function search(Request $req, Response $res): void
    {
        try {
            $filters = $req->getQueryParams();
            $materials = $this->service->searchMaterials($filters);
            $res->status(200)->json($materials);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PATCH /material/{id}/stock
     * Actualiza el stock/unidades de un material
     */
    public function updateStock(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            
            if (!isset($data['unidades'])) {
                $res->errorJson(["El campo 'unidades' es requerido"], 400);
                return;
            }

            $material = $this->service->updateStock($id, (int)$data['unidades']);
            $res->status(200)->json($material, "Stock actualizado exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

}