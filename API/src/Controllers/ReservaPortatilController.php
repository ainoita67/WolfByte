<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\ReservaPortatilService;

class ReservaPortatilController
{
    private ReservaPortatilService $service;

    public function __construct()
    {
        $this->service = new ReservaPortatilService();
    }

    /**
     * GET /reservaPortatiles
     * Devuelve todas las reservas de portátiles
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservas();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /reservaPortatiles/{id}
     * Devuelve las reservas de un portátil específico
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $reservas = $this->service->getReservasByMaterial($id);
            $res->status(200)->json($reservas);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /reservaPortatiles/usuario/{id_usuario}
     * Devuelve las reservas de un usuario específico
     */
    public function showByUsuario(Request $req, Response $res, string $id_usuario): void
    {
        try {
            $reservas = $this->service->getReservasByUsuario((int)$id_usuario);
            $res->status(200)->json($reservas);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /reservaPortatiles/reserva/{idReserva}
     * Devuelve una reserva específica por ID de reserva
     */
    public function showById(Request $req, Response $res, string $idReserva): void
    {
        try {
            $reserva = $this->service->getReservaById((int)$idReserva);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            if ($e->getCode() === 404) {
                $res->status(404)->json(['error' => $e->getMessage()]);
            } else {
                $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
            }
        }
    }

    /**
     * POST /reservaPortatiles/disponibilidad
     * Verifica disponibilidad de portátiles
     */
    public function disponibilidad(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $disponibilidad = $this->service->checkDisponibilidad($data);
            $res->status(200)->json($disponibilidad);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * POST /reservaPortatiles
     * Crea una nueva reserva de portátil
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->createReserva($data);
            $res->status(201)->json(
                ['id' => $result['id']],
                $result['message']
            );
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->errorJson(app_debug() ? $e->getMessage() : "Error interno del servidor", 500);
        }
    }

    /**
     * PUT /reservaPortatiles/{id}
     * Actualiza una reserva completa
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->updateReserva((int)$id, $data);
            
            if ($result['status'] === 'no_changes') {
                $res->status(200)->json([], $result['message']);
                return;
            }

            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->getErrors()]);
        } catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * PATCH /reservaPortatiles/{id}
     * Actualiza solo las fechas de una reserva
     */
    public function patch(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->updateFechas((int)$id, $data);
            
            if ($result['status'] === 'no_changes') {
                $res->status(200)->json([], $result['message']);
                return;
            }

            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->getErrors()]);
        } catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * PATCH /reservaPortatiles/{id}/unidades
     * Actualiza solo las unidades de una reserva
     */
    public function updateUnidades(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->updateUnidades((int)$id, $data);
            
            if ($result['status'] === 'no_changes') {
                $res->status(200)->json([], $result['message']);
                return;
            }

            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->getErrors()]);
        } catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * DELETE /reservaPortatiles/{id}
     * Elimina una reserva
     */
    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            $this->service->deleteReserva((int)$id);
            $res->status(200)->json([], "Reserva eliminada correctamente");
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }
}