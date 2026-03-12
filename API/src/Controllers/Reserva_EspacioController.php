<?php
declare(strict_types=1);

namespace Controllers;

use Services\Reserva_EspacioService;
use PDO;
use Exception;

class Reserva_EspacioController
{
    private Reserva_EspacioService $service;

    public function __construct(PDO $db)
    {
        $this->service = new Reserva_EspacioService(
            new \Models\Reserva_EspacioModel($db)
        );
    }

    public function index(): void
    {
        
        try {
            $data = $this->service->getReservas();

            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al obtener reservas'
            ]);
        }
    }
}
