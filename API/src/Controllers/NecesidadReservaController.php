<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\NecesidadReservaService;

class NecesidadReservaController
{
    private NecesidadReservaService $service;

    public function __construct()
    {
        $this->service = new NecesidadReservaService();
    }

    public function index(Request $req, Response $res): void
    {
        try {
            $necesidades = $this->service->getAllNecesidades();
            $res->status(200)->json($necesidades);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function show(Request $req, Response $res, array $args): void
    {
        try {
            $necesidad = $this->service->getNecesidadById((int)$args['id']);
            $res->status(200)->json($necesidad);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $necesidad = $this->service->createNecesidad($data);
            $res->status(201)->json($necesidad);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function update(Request $req, Response $res, $args): void
    {
        try {
            $id = is_array($args) ? (int)$args['id'] : (int)$args;
            $data = $req->getBody();
            $necesidad = $this->service->updateNecesidad($id, $data);
            $res->status(200)->json($necesidad);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function destroy(Request $req, Response $res, $id): Response
    {
        try {
            $this->service->deleteNecesidad((int)$id);
            return $res->status(204)->json([]);
        } catch (Throwable $e) {
            return $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}