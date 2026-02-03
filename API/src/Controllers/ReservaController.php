<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\ReservaService;
use Throwable;
use Validation\ValidationException;

class ReservaController
{
    private ReservaService $service;

    public function __construct()
    {
        $this->service = new ReservaService();
    }

    public function misReservas(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservas(); // Para pruebas sin sesión
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
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
            $data['id_usuario'] = 3; // Usuario de prueba
            $reserva = $this->service->createReserva($data);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function update(Request $req, Response $res, int $id): void
    {
        try {
            $data = $req->getBody();
            $updated = $this->service->updateReserva($id, $data);
            $res->status(200)->json($updated);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function destroy(Request $req, Response $res, int $id): void
    {
        try {
            $this->service->deleteReserva($id);
            $res->status(200)->json(['message' => 'Reserva eliminada correctamente']);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}
