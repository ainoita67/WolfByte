<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\EdificioService;

class EdificioController
{
    private EdificioService $service;

    public function __construct()
    {
        $this->service = new EdificioService();
    }

    /**
     * GET /edificios
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $edificios = $this->service->getAllEdificios();
            $res->status(200)->json($edificios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /edificios/{id}
     */
    public function show(Request $req, Response $res, string $id): void  // Cambiado a string $id
    {
        try {
            $edificio = $this->service->getEdificioById((int) $id);
            $res->status(200)->json($edificio);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /edificios
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            
            if (!isset($data['nombre_edificio']) || empty(trim($data['nombre_edificio']))) {
                $res->errorJson('El nombre del edificio es obligatorio', 422);
                return;
            }

            $edificio = $this->service->createEdificio($data);
            
            $res->status(201)->json([
                'status' => 'success',
                'data' => $edificio,
                'message' => 'Edificio creado correctamente'
            ]);
            
        } catch (ValidationException $e) {
            $res->status(422)->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            $res->status(500)->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * PUT /edificios/{id}
     * IMPORTANTE: El parámetro debe ser string $id, no array $params
     */
    public function update(Request $req, Response $res, string $id): void  // Cambiado a string $id
    {
        try {
            $data = $req->getBody();
            
            if (!isset($data['nombre_edificio']) || empty(trim($data['nombre_edificio']))) {
                $res->errorJson('El nombre del edificio es obligatorio', 422);
                return;
            }

            $edificio = $this->service->updateEdificio((int)$id, $data);
            
            $res->status(200)->json([
                'status' => 'success',
                'data' => $edificio,
                'message' => 'Edificio actualizado correctamente'
            ]);
            
        } catch (ValidationException $e) {
            $res->status(422)->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            $res->status(500)->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * DELETE /edificios/{id}
     */
    public function destroy(Request $req, Response $res, string $id): void  // Cambiado a string $id
    {
        try {
            $this->service->deleteEdificio((int)$id);
            
            $res->status(200)->json([
                'status' => 'success',
                'message' => 'Edificio eliminado correctamente'
            ]);
            
        } catch (ValidationException $e) {
            $res->status(422)->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            $res->status(500)->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}