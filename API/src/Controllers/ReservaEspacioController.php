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

    public function index(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservas();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function showEspacio(Request $req, Response $res, string $id): void
    {
        try {
            $data = $this->service->getReservasPorEspacio($id);
            $res->status(200)->json($data);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

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
            $this->serviceNecesidad->updateNecesidad((int)$id, $data);
            $reserva = $this->service->updateReserva((int)$id, $data);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function update(Request $req, Response $res, int $id): void
{
    try {
        $data = $req->getBody();

        $reserva = $this->service->updateReserva($id, $data);

        $res->status(200)->json($reserva);

    } catch (ValidationException $e) {

        $res->errorJson($e->getMessage(), 422);

    } catch (Throwable $e) {

        $res->errorJson($e->getMessage(), 500);

    }
}
}