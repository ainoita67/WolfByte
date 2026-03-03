<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
use Services\LiberacionPuntualService;
use Throwable;
use Validation\ValidationException;

class LiberacionPuntualController
{
    private LiberacionPuntualService $service;

    public function __construct()
    {
        $this->service = new LiberacionPuntualService();
    }

    /**
     * GET /liberaciones
     * Devuelve todas las liberaciones puntuales
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $liberaciones = $this->service->getAllLiberacionesPuntuales();
            $res->status(200)->json($liberaciones);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /liberaciones/{id}
     * Devuelve una liberación puntual por ID
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $liberacion = $this->service->getLiberacionPuntualById($id);
            $res->status(200)->json($liberacion);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /liberaciones/recurso/{id_recurso}
     * Muestra todas las liberaciones puntuales de un recurso por ID de recurso
     */
    public function showByRecurso(Request $req, Response $res, string $id_recurso): void
    {
        try {
            $liberaciones = $this->service->getLiberacionPuntualByIdRecurso($id_recurso);
            $res->status(200)->json($liberaciones);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /liberaciones/usuario/{id_usuario}
     * Muestra todas las liberaciones puntuales de un usuario por ID de usuario
     */
    public function showByUsuario(Request $req, Response $res, string $id_usuario): void
    {
        try {
            $liberaciones = $this->service->getLiberacionPuntualByIdUsuario($id_usuario);
            $res->status(200)->json($liberaciones);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /liberaciones
     * Crea una nueva liberación puntual
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $liberacion = $this->service->createLiberacionPuntual($data);
            $res->status(201)->json($liberacion);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /liberaciones/reserva/{id_reserva}
     * Crea una nueva liberación puntual ligada a una reserva por ID de reserva
     */
    public function storeByReserva(Request $req, Response $res, string $id_reserva): void
    {
        try {
            $data = $req->getBody();
            $liberacion = $this->service->createLiberacionPuntualByReserva($id_reserva, $data);
            $res->status(201)->json($liberacion);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PUT /liberaciones/{id}
     * Modifica totalmente una liberación puntual por ID
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $liberacion = $this->service->updateLiberacionPuntual($id, $data);
            $res->status(201)->json($liberacion);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

     /**
     * DELETE /liberaciones/{id}
     * Elimina una liberación puntual por ID
     */
    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            $liberacion = $this->service->deleteLiberacionPuntual($id);
            $res->status(201)->json($liberacion);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}