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

    /**
     * POST /reserva-espacio
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();

            // Crea reserva y reserva de espacio en un solo flujo
            $reservaEspacio = $this->service->createReservaEspacio($data);

            $res->status(201)->json($reservaEspacio);
        }catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        }
        catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reserva-espacio
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
     * GET /reserva-espacio/espacio/{id}
     */
    public function showByEspacio(Request $req, Response $res, $idEspacio): void
    {
        try {
            $reservas = $this->service->getReservasByEspacio($idEspacio);
            $res->status(200)->json($reservas);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}