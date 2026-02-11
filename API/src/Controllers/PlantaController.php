<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\PlantaService;
use Throwable;
use Validation\ValidationException;

class PlantaController
{
    private PlantaService $service;

    public function __construct()
    {
        $this->service = new PlantaService();
    }

    /**
     * GET /plantas
     * Devuelve todas las plantas con su edificio
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $plantas = $this->service->getAllPlantas();
            $res->status(200)->json($plantas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /plantas/{id_edificio}
     * Devuelve las plantas de un edificio específico
     */
    public function showByEdificio(Request $req, Response $res, string $idEdificio): void
    {
        try {
            $plantas = $this->service->getPlantasByEdificio((int)$idEdificio);
            $res->status(200)->json($plantas);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /plantas/{id_edificio}
     * Agrega una planta al edificio
     */
    public function store(Request $req, Response $res, string $idEdificio): void
    {
        try {
            $data = $req->getBody();
            $planta = $this->service->createPlanta((int)$idEdificio, $data);
            $res->status(201)->json($planta, "Planta creada exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PUT /plantas/{id_edificio}?numero_planta={numero}
     * Modifica los datos de una planta específica
     * IMPORTANTE: Necesitamos el número de planta como query parameter
     */
    public function update(Request $req, Response $res, string $idEdificio): void
    {
        try {
            // Obtener número de planta del query parameter
            $numeroPlanta = $req->getParam('numero_planta');
            
            if (!$numeroPlanta) {
                $res->errorJson(["El parámetro 'numero_planta' es requerido"], 400);
                return;
            }

            $data = $req->getBody();
            $planta = $this->service->updatePlanta((int)$numeroPlanta, (int)$idEdificio, $data);
            $res->status(200)->json($planta, "Planta actualizada exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /plantas/{id_edificio}?numero_planta={numero}
     * Elimina una planta específica
     */
    public function destroy(Request $req, Response $res, string $idEdificio): void
    {
        try {
            // Obtener número de planta del query parameter
            $numeroPlanta = $req->getParam('numero_planta');
            
            if (!$numeroPlanta) {
                $res->errorJson(["El parámetro 'numero_planta' es requerido"], 400);
                return;
            }

            $this->service->deletePlanta((int)$numeroPlanta, (int)$idEdificio);
            $res->status(200)->json([], "Planta eliminada exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /plantas/{id_edificio}/detalles?numero_planta={numero}
     * Obtiene detalles específicos de una planta
     */
    public function showDetails(Request $req, Response $res, string $idEdificio): void
    {
        try {
            // Obtener número de planta del query parameter
            $numeroPlanta = $req->getParam('numero_planta');
            
            if (!$numeroPlanta) {
                $res->errorJson(["El parámetro 'numero_planta' es requerido"], 400);
                return;
            }

            $planta = $this->service->getPlantaDetails((int)$numeroPlanta, (int)$idEdificio);
            $res->status(200)->json($planta);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}