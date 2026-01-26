<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\NecesidadReservaService;
use Throwable;
use Validation\ValidationException;

class NecesidadReservaController
{
    private NecesidadReservaService $service;

    public function __construct()
    {
        $this->service = new NecesidadReservaService();
    }

    /**
     * Crear una nueva necesidad de reserva
     * POST /necesidad-reserva
     * Body JSON: { "usuario_id": 1, "fecha": "2026-01-20", "hora": "12:00", "cantidad_personas": 2, "tipo_servicio": "comida", "notas": "opcional" }
     */
    public function create(Request $req, Response $res): void
    {
        try {
            $data = $req->json(); 

            if (!isset($data['usuario_id'])) {
                $res->status(422)->json(['errors' => ['usuario_id' => 'Campo requerido']], "Falta usuario_id");
                return;
            }

            $reservation = $this->service->create($data);

            $res->status(201)->json([
                'reservation' => $reservation
            ], "Necesidad de reserva creada correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * Listar necesidades de reserva de un usuario
     * GET /necesidad-reserva?usuario_id=1
     */
    public function list(Request $req, Response $res): void
    {
        $usuarioId = $_GET['usuario_id'] ?? null;

        if (!$usuarioId) {
            $res->status(422)->json(['errors' => ['usuario_id' => 'Campo requerido']], "Falta usuario_id");
            return;
        }

        try {
            $reservations = $this->service->getByUsuario((int)$usuarioId);
            $res->status(200)->json(['reservations' => $reservations], "Necesidades de reserva del usuario");
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * Mostrar una necesidad de reserva
     * GET /necesidad-reserva/{id}?usuario_id=1
     */
    public function show(Request $req, Response $res): void
    {
        $usuarioId = $_GET['usuario_id'] ?? null;
        $id = $req->param('id');

        if (!$usuarioId) {
            $res->status(422)->json(['errors' => ['usuario_id' => 'Campo requerido']], "Falta usuario_id");
            return;
        }

        $reservation = $this->service->getById((int)$id);

        if (!$reservation || $reservation['usuario_id'] !== (int)$usuarioId) {
            $res->status(404)->json([], "Reserva no encontrada");
            return;
        }

        $res->status(200)->json(['reservation' => $reservation], "Reserva encontrada");
    }

    /**
     * Actualizar una necesidad de reserva
     * PUT /necesidad-reserva/{id}
     * Body JSON: { "usuario_id": 1, "fecha": "...", "hora": "...", "cantidad_personas": ..., "tipo_servicio": "...", "notas": "..." }
     */
    public function update(Request $req, Response $res): void
    {
        try {
            $data = $req->json(); 
            $id = $req->param('id');

            if (!isset($data['usuario_id'])) {
                $res->status(422)->json(['errors' => ['usuario_id' => 'Campo requerido']], "Falta usuario_id");
                return;
            }

            $updated = $this->service->update((int)$id, $data);

            $res->status(200)->json(['reservation' => $updated], "Reserva actualizada correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * Cancelar una necesidad de reserva
     * DELETE /necesidad-reserva/{id}?usuario_id=1
     */
    public function cancel(Request $req, Response $res): void
    {
        try {
            $usuarioId = $_GET['usuario_id'] ?? null;
            $id = $req->param('id');

            if (!$usuarioId) {
                $res->status(422)->json(['errors' => ['usuario_id' => 'Campo requerido']], "Falta usuario_id");
                return;
            }

            $this->service->delete((int)$usuarioId, (int)$id);

            $res->status(200)->json([], "Necesidad de reserva cancelada correctamente");

        } catch (Throwable $e) {
            $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }
}
