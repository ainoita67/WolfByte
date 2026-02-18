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
            // Devolver directamente el array de plantas (como espera el frontend)
            $res->status(200)->json($plantas);
        } catch (Throwable $e) {
            $res->status(500)->json(['error' => $e->getMessage()]);
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
            // Devolver directamente el array de plantas
            $res->status(200)->json($plantas);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->status(500)->json(['error' => $e->getMessage()]);
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
            
            // Verificar que viene nombre_planta
            if (!isset($data['nombre_planta']) || empty($data['nombre_planta'])) {
                $res->status(422)->json(['error' => "El campo 'nombre_planta' es requerido"]);
                return;
            }
            
            // Verificar que viene numero_planta
            if (!isset($data['numero_planta']) || $data['numero_planta'] === '') {
                $res->status(422)->json(['error' => "El campo 'numero_planta' es requerido"]);
                return;
            }
            
            $planta = $this->service->createPlanta((int)$idEdificio, $data);
            // Devolver la planta creada directamente
            $res->status(201)->json($planta);
        } catch (ValidationException $e) {
            $res->status(422)->json(['error' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->status(500)->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * PUT /plantas/{id_edificio}?numero_planta={numero}
     * Modifica los datos de una planta específica
     */
    public function update(Request $req, Response $res, string $idEdificio): void
    {
        try {
            // Obtener número de planta del query parameter
            $numeroPlanta = $req->getParam('numero_planta');
            
            if (!$numeroPlanta) {
                $res->status(400)->json(['error' => "El parámetro 'numero_planta' es requerido"]);
                return;
            }

            $data = $req->getBody();
            $planta = $this->service->updatePlanta((int)$numeroPlanta, (int)$idEdificio, $data);
            // Devolver la planta actualizada directamente
            $res->status(200)->json($planta);
        } catch (ValidationException $e) {
            $res->status(422)->json(['error' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->status(500)->json(['error' => $e->getMessage()]);
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
                $res->status(400)->json(['error' => "El parámetro 'numero_planta' es requerido"]);
                return;
            }

            $this->service->deletePlanta((int)$numeroPlanta, (int)$idEdificio);
            $res->status(200)->json(['message' => 'Planta eliminada exitosamente']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['error' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->status(500)->json(['error' => $e->getMessage()]);
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
                $res->status(400)->json(['error' => "El parámetro 'numero_planta' es requerido"]);
                return;
            }

            $planta = $this->service->getPlantaDetails((int)$numeroPlanta, (int)$idEdificio);
            // Devolver los detalles directamente
            $res->status(200)->json($planta);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->status(500)->json(['error' => $e->getMessage()]);
        }
    }
}