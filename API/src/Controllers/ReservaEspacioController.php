<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\ReservaEspacioService;
use Throwable;
use Validation\ValidationException;

class ReservaEspacioController
{
    private ReservaEspacioService $service;

    public function __construct()
    {
        $this->service = new ReservaEspacioService();
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
    public function showByEspacio(Request $req, Response $res, $idEspacio): void
    {
        try {
            $reservas = $this->service->getReservasByEspacio($idEspacio);
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Devuelve una reserva específica por su ID
    public function show(Request $req, Response $res, int $id): void
    {
        try {
            $reserva = $this->service->getReservaById($id);
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
    public function update(Request $req, Response $res, int $id): void
    {
        try {
            $data = $req->getBody();
            $reserva = $this->service->updateReserva($id, $data);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Elimina una reserva
    public function destroy(Request $req, Response $res, int $id): void
    {
        try {
            $this->service->deleteReserva($id);
            $res->status(200)->json(['message' => 'Reserva eliminada correctamente']);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getErrors()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}
