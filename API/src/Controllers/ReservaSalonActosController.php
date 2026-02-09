<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\ReservaEspacioService;
use Throwable;

class ReservaSalonActosController
{
    private ReservaEspacioService $service;

    public function __construct()
    {
        $this->service = new ReservaEspacioService();
    }

    /**
     * GET /reservas-salon-actos
     * Devuelve todas las reservas del Salón de Actos
     */
    public function index(Request $req, Response $res): void
    {
        try {

            $data = $this->service->getReservasPorEspacio("salon");

            $res->status(200)->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Throwable $e) {
            $res->errorJson(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }
}
