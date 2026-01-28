<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
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

    /**
     * GET /mis-reservas
     * Devuelve las reservas del usuario autenticado
     */
    public function misReservas(Request $req, Response $res): void
    {
        try {
            $usuario = Session::get('user');

            if (!$usuario) {
                $res->status(401)->json(['error' => 'No autenticado']);
                return;
            }

            $reservas = $this->service->getReservasUsuario((int)$usuario['id_usuario']);

            $res->status(200)->json($reservas);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reservas
     * Devuelve todas las reservas
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservas();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reservas/{id}
     * Devuelve los detalles de una reserva por ID
     */
    public function show(Request $req, Response $res, int $id): void
    {
        try {
            $reserva = $this->service->getReservaById($id);

            if (!$reserva) {
                $res->status(404)->json(['error' => 'Reserva no encontrada']);
                return;
            }

            $res->status(200)->json($reserva);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /reservas
     * Crea una nueva reserva
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();


            $reserva = $this->service->createReserva($data);

            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PUT /reservas/{id}
     * Actualiza una reserva existente
     */
    public function update(Request $req, Response $res, int $id): void
    {
        try {
            $data = $req->all();

            $reserva = $this->service->getReservaById($id);
            if (!$reserva) {
                $res->status(404)->json(['error' => 'Reserva no encontrada']);
                return;
            }

            $updated = $this->service->updateReserva($id, $data);

            $res->status(200)->json($updated);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /reservas/{id}
     * Elimina una reserva por ID
     */
    public function destroy(Request $req, Response $res, int $id): void
    {
        try {
            $reserva = $this->service->getReservaById($id);
            if (!$reserva) {
                $res->status(404)->json(['error' => 'Reserva no encontrada']);
                return;
            }

            $this->service->deleteReserva($id);

            $res->status(200)->json(['message' => 'Reserva eliminada correctamente']);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}
