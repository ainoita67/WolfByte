<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\ReservaMaterialService;
use Throwable;
use Validation\ValidationException;

class ReservaMaterialController
{
    private ReservaMaterialService $service;

    public function __construct()
    {
        $this->service = new ReservaMaterialService();
    }

    // Devuelve todas las reservas de espacios
    public function index(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservas();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Devuelve todas las reservas de un espacio específico
    public function showMaterial(Request $req, Response $res, string $id): void
    {
        try {
            $data = $this->service->getReservasPorMaterial($id);
            $res->status(200)->json($data);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Devuelve una reserva específica por su ID
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaById((int)$id);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Devuelve reservas pendientes de autorizar
    public function pendientes(Request $req, Response $res): void
    {
        try {
            $reserva = $this->service->getReservasPendientes();
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Devuelve reservas próximas al día de hoy
    public function proximas(Request $req, Response $res): void
    {
        try {
            $reserva = $this->service->getReservasProximas();
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Crea una nueva reserva
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $reserva = $this->service->createReserva($data);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        }
        catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Actualiza una reserva existente
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $reserva = $this->service->updateReserva((int)$id, $data);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Elimina una reserva
    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            $this->service->deleteReserva((int)$id);
            $res->status(200)->json(['message' => 'Reserva eliminada correctamente']);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}
