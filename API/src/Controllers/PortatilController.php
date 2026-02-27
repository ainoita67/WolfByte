<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\PortatilService;

class PortatilController
{
    private PortatilService $service;

    public function __construct()
    {
        $this->service = new PortatilService();
    }

    /**
     * ===========================================
     * MATERIALES (CARROS DE PORTÁTILES)
     * ===========================================
     */

    /**
     * GET /portatiles/materiales
     * Devuelve todos los materiales (carros de portátiles)
     */
    public function materiales(Request $req, Response $res): void
    {
        try {
            $materiales = $this->service->getAllMateriales();
            $res->status(200)->json($materiales);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /portatiles/materiales/{id}
     * Devuelve un material específico por ID
     */
    public function material(Request $req, Response $res, string $id): void
    {
        try {
            $material = $this->service->getMaterialById($id);
            $res->status(200)->json($material);
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
     * POST /portatiles/materiales
     * Crea un nuevo material
     */
    public function createMaterial(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->createMaterial($data);
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
     * PUT /portatiles/materiales/{id}
     * Actualiza un material por ID
     */
    public function updateMaterial(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->updateMaterial($id, $data);
            
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
     * DELETE /portatiles/materiales/{id}
     * Elimina un material por ID
     */
    public function deleteMaterial(Request $req, Response $res, string $id): void
    {
        try {
            $this->service->deleteMaterial($id);
            $res->status(200)->json([], "Material eliminado correctamente");
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * ===========================================
     * RESERVAS DE PORTÁTILES
     * ===========================================
     */

    /**
     * GET /portatiles/reservas
     * Devuelve todas las reservas de portátiles
     */
    public function reservas(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservas();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /portatiles/reservas/usuario/{id_usuario}
     * Devuelve las reservas de portátiles de un usuario específico
     */
    public function reservasByUsuario(Request $req, Response $res, string $id_usuario): void
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
     * GET /portatiles/reservas/{id}
     * Devuelve una reserva específica por ID de reserva
     */
    public function reserva(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaById((int)$id);
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
     * POST /portatiles/reservas
     * Crea una nueva reserva de portátil
     */
    public function createReserva(Request $req, Response $res): void
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
     * POST /portatiles/reservas/disponibilidad
     * Verifica disponibilidad de un portátil en un rango de fechas
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
     * PUT /portatiles/reservas/{id}
     * Actualiza una reserva completa por ID
     */
    public function updateReserva(Request $req, Response $res, string $id): void
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
     * PATCH /portatiles/reservas/{id}
     * Actualiza parcialmente una reserva (solo fechas)
     */
    public function patchReserva(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->patchFechas((int)$id, $data);
            
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
     * PATCH /portatiles/reservas/{id}/unidades
     * Actualiza solo el número de unidades de una reserva
     */
    public function patchUnidades(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $result = $this->service->patchUnidades((int)$id, $data);
            
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
     * DELETE /portatiles/reservas/{id}
     * Elimina una reserva por ID
     */
    public function deleteReserva(Request $req, Response $res, string $id): void
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