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

    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();

            $reservaEspacio = $this->service->createReservaEspacio($data);

            $res->status(201)->json($reservaEspacio);
        } catch (ValidationException $e) {
            // PASAMOS EL ARRAY DE ERRORES, NO EL MENSAJE
            $res->errorJson($e->getErrors(), 422);
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

    public function showByEspacio(Request $req, Response $res, $idEspacio): void
    {
        try {
            $reservas = $this->service->getReservasByEspacio($idEspacio);
            $res->status(200)->json($reservas);
        } catch (ValidationException $e) {
            // Devuelve un array con los errores reales
            $res->errorJson(['errors' => $e->getErrors()], 422);
        }catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
    
}
