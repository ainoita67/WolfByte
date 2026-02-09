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

public function verificarDisponibilidad(string $inicio, string $fin, ?int $idExcluir = null): bool
{
    try {
        // Formato de fechas de entrada (debug)
        error_log("Verificando: {$inicio} -> {$fin}, Excluir: " . ($idExcluir ?? 'ninguno'));
        
        // Consulta SQL simplificada pero efectiva
        $sql = "SELECT id_reserva, inicio, fin FROM Reserva";
        $params = [];
        
        if ($idExcluir) {
            $sql .= " WHERE id_reserva != ?";
            $params[] = $idExcluir;
        }
        
        $reservas = $this->db->query($sql, $params)->fetchAll();
        
        // Convertir fechas de la nueva reserva
        $nuevoStart = strtotime($inicio);
        $nuevoEnd = strtotime($fin);
        
        foreach ($reservas as $reserva) {
            $existenteStart = strtotime($reserva['inicio']);
            $existenteEnd = strtotime($reserva['fin']);
            
            // Lógica de solapamiento
            $haySolapamiento = 
                // Caso A: Nueva empieza dentro de existente
                ($nuevoStart >= $existenteStart && $nuevoStart < $existenteEnd) ||
                // Caso B: Nueva termina dentro de existente
                ($nuevoEnd > $existenteStart && $nuevoEnd <= $existenteEnd) ||
                // Caso C: Nueva contiene completamente existente
                ($nuevoStart <= $existenteStart && $nuevoEnd >= $existenteEnd);
            
            if ($haySolapamiento) {
                error_log("SOLAPAMIENTO detectado con reserva ID: " . $reserva['id_reserva']);
                error_log("Existente: " . date('Y-m-d H:i:s', $existenteStart) . " - " . date('Y-m-d H:i:s', $existenteEnd));
                error_log("Nueva: " . date('Y-m-d H:i:s', $nuevoStart) . " - " . date('Y-m-d H:i:s', $nuevoEnd));
                return false;
            }
        }
        
        return true;
        
    } catch (\Exception $e) {
        error_log("Exception en verificarDisponibilidad: " . $e->getMessage());
        // Por seguridad, si hay error no permitir el cambio
        return false;
    }
}



}
