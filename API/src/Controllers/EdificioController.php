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
     * Devuelve todos los edificios
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
     * Devuelve un edificio por ID
     */
    public function show(Request $req, Response $res, array $args): void
    {
        try {
            $edificio = $this->service->getEdificioById((int) $args['id']);
            $res->status(200)->json($edificio);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /edificios
     * Crea un nuevo edificio
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $edificio = $this->service->createEdificio($data);
            $res->status(201)->json($edificio);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PUT /edificios/{id}
     * Actualiza un edificio existente
     */
    public function update(Request $req, Response $res, $args): void
    {
        try {
            // El router pasa el ID como string
            $id = is_array($args) ? (int) $args['id'] : (int) $args;

            $data = $req->getBody();

            $edificio = $this->service->updateEdificio($id, $data);

            $res->status(200)->json($edificio);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }


    /**
     * DELETE /edificios/{id}
     * Elimina un edificio
     */
    public function destroy(Request $req, Response $res, $id): Response
    {
        try {
            $this->service->deleteEdificio((int) $id);
            return $res->status(204); // sin JSON
        } catch (Throwable $e) {
            return $res->errorJson(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }
}
