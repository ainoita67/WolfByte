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


public function updateFechas($request, $response, $id)
{
    $body = json_decode(file_get_contents('php://input'), true);

    if (!$body) {
        return $response
            ->status(400)
            ->json([
                'status' => 'error',
                'message' => 'Body vacío o inválido'
            ]);
    }

    $start = $body['inicio']
          ?? $body['newStart']
          ?? null;
    
    $end = $body['fin']
        ?? $body['newEnd']
        ?? null;
    
    if ($start === null || $end === null) {
        return $response
            ->status(400)
            ->json([], 'Fechas inválidas');
    }


    $ok = $this->service->updateFechasReserva((int)$id, $start, $end);

    if (!$ok) {
        return $response
            ->status(500)
            ->json([
                'status' => 'error',
                'message' => 'No se pudo guardar el cambio'
            ]);
    }

    return $response->json([
        'status' => 'success'
    ]);
}

public function verificarDisponibilidad(Request $req, Response $res): void
{
    try {
        $body = $req->body();
        
        $inicio = $body['inicio'] ?? null;
        $fin = $body['fin'] ?? null;
        $idExcluir = $body['id_excluir'] ?? null;
        
        if (!$inicio || !$fin) {
            $res->status(400)->json([
                'status' => 'error',
                'message' => 'Fechas requeridas'
            ]);
            return;
        }
        
        $disponible = $this->service->verificarDisponibilidad(
            $inicio, 
            $fin, 
            $idExcluir ? (int)$idExcluir : null
        );
        
        $res->json([
            'status' => 'success',
            'disponible' => $disponible,
            'message' => $disponible 
                ? 'Horario disponible' 
                : 'Horario no disponible (se solapa con otra reserva)'
        ]);
        
    } catch (ValidationException $e) {
        $res->errorJson($e->getMessage(), 400);
    } catch (\Throwable $e) {
        $res->errorJson($e->getMessage(), 500);
    }
}



}
