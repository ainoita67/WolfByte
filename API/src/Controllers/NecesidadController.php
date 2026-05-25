<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\NecesidadService;

class NecesidadController
{
    private NecesidadService $service;

    public function __construct()
    {
        $this->service = new NecesidadService();
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
}