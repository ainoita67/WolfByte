<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
use Services\ReservaEspacioService;
use Throwable;
use Validation\ValidationException;
use Validation\Validation;

class ReservaEspacioController
{
    private ReservaEspacioService $service;

    public function __construct()
    {
        $this->service = new ReservaEspacioService();
    }

    /**
     * GET /reservaEspacio
     * Devuelve todas las reservas de espacio
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
     * GET /mis-reservas-espacio
     * Devuelve las reservas de espacio del usuario autenticado
     */
    public function misReservas(Request $req, Response $res): void
    {
        try {
            $usuario = Session::getUser();
            
            if (!$usuario || !isset($usuario['id_usuario'])) {
                $res->status(401)->json([], "Usuario no autenticado");
                return;
            }

            $reservas = $this->service->getMisReservas((int)$usuario['id_usuario']);
            $res->status(200)->json($reservas);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reservaEspacio/{id}
     * Devuelve una reserva específica por ID
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaById((int)$id);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reserva-espacio/espacio/{id}
     * Devuelve reservas por espacio
     */
    public function showByEspacio(Request $req, Response $res, string $id): void
    {
        try {
            $reservas = $this->service->getReservasByEspacio($id);
            $res->status(200)->json($reservas);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /reservaEspacio
     * Crea una nueva reserva de espacio
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->json();
            error_log("Datos recibidos en controller: " . print_r($data, true));
            
            // Si no viene id_usuario en la request, usar el usuario autenticado
            if (!isset($data['id_usuario'])) {
                $usuario = Session::getUser();
                if ($usuario && isset($usuario['id_usuario'])) {
                    $data['id_usuario'] = $usuario['id_usuario'];
                }
            }

            $reserva = $this->service->createReservaEspacio($data);
            $res->status(201)->json($reserva, "Reserva creada exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PUT /reservaEspacio/{id}
     * Actualiza una reserva de espacio existente
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json();
            $reserva = $this->service->updateReservaEspacio((int)$id, $data);
            $res->status(200)->json($reserva, "Reserva actualizada exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PATCH /reservaEspacio/{id}
     * Cambia solo las fechas de una reserva
     */
    public function cambiarFechas(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json();
            $reserva = $this->service->cambiarFechasReserva((int)$id, $data);
            $res->status(200)->json($reserva, "Fechas de reserva actualizadas exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * DELETE /reservaEspacio/{id}
     * Elimina una reserva de espacio
     */
    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            $this->service->deleteReservaEspacio((int)$id);
            $res->status(200)->json([], "Reserva eliminada exitosamente");
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}