<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
use Services\RecursoService;
use Throwable;
use Validation\ValidationException;

class RecursoController
{
    private RecursoService $service;

    public function __construct()
    {
        $this->service = new RecursoService();
    }

    /**
     * GET /recurso
     * Devuelve todos los recursos
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $recursos = $this->service->getAllRecursos();
            $res->status(200)->json($recursos);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }


    /**
     * GET /recurso/activos
     * Devuelve todos los recursos activos
     */
    public function indexActivos(Request $req, Response $res): void
    {
        try {
            $recursos = $this->service->getAllRecursosActivos();
            $res->status(200)->json($recursos);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PATCH /recursos/{id}/activo
     * Cambia el estado de activo de un recurso por ID
     */
    public function updateActivar(Request $req, Response $res, string $id): void
    {
        try {
            $recurso = $this->service->activoRecurso($id);
            $res->status(201)->json($recurso);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}