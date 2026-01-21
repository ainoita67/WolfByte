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
     * Reservas del usuario logueado
     */
    public function misReservas(Request $req, Response $res): void
    {
        try {
            $usuario = $_SESSION['user'] ?? null;

            if (!$usuario) {
                $res->status(401)->json([], "No autenticado");
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
}
