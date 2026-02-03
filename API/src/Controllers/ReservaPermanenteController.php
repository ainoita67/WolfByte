<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
use Services\ReservaPermanenteService;
use Throwable;
use Validation\ValidationException;

class ReservaPermanenteController
{
    private ReservaPermanenteService $service;

    public function __construct()
    {
        $this->service = new ReservaPermanenteService();
    }

    /**
     * GET /reservas_permanentes
     * Devuelve todas las reservas permanentes activas
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservasPermanentes();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reservas_permanentes/{id}
     * Devuelve una reserva permanente por ID
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaPermanenteById($id);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reservas_permanentes/{id_recurso}
     * Muestra todas las reservas permanentes activas por recurso
     */
    public function showActivas(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaPermanenteById($id);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /reservas_permanentes
     * Crea una nueva reserva permanente
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $reserva = $this->service->createReservaPermanente($data);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PUT /reservas_permanentes/{id}
     * Modifica totalmente una reserva permanente por ID
     */
    public function update(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $reserva = $this->service->updateReservaPermanente($data);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PATCH /reservas_permanentes/{id}/activar
     * Activa una reserva permanente por ID
     */
    public function activate(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->activarReservaPermanente($id);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PATCH /reservas_permanentes/{id}/desactivar_todo
     * Desactiva todas las reservas permanentes
     */
    public function deactivate(Request $req, Response $res): void
    {
        try {
            $reserva = $this->service->desactivarReservasPermanentes();
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}