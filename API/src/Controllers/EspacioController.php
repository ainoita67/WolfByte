<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\EspacioService;

class EspacioController
{
    private EspacioService $service;

    public function __construct()
    {
        $this->service = new EspacioService();
    }

    public function index(Request $req, Response $res): void
    {
        try {
            // Obtener todos los espacios
            $espacios = $this->service->getAllEspacios();
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $req, Response $res, string $id): void
    {
        try {
            // Obtener espacio por ID
            $espacio = $this->service->getEspacioById($id);
            $res->status(200)->json($espacio);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
            return;
        } catch (Throwable $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            $res->errorJson($e->getMessage(), $status);
        }
    }

    public function store(Request $req, Response $res): void
    {
        try {
            // Crear nuevo espacio
            $result = $this->service->createEspacio($req->json());

            $res->status(201)->json(
                $result,
                "Espacio creado correctamente"
            );

        } catch (ValidationException $e) {
            $res->status(422)->json(
                ['errors' => $e->errors],
                "Errores de validación"
            );
            return;

        } catch (Throwable $e) {
            $code = $e->getCode() ?: 500;
            $res->errorJson(app_debug() ? $e->getMessage() : "Error interno del servidor", $code);
            return;
        }
    }

    public function update(Request $req, Response $res, string $id): void
    {
        try {
            // Actualizar espacio existente
            $result = $this->service->updateEspacio($id, $req->json());

            // Depende de lo recibido del servicio envía no_changes o updated
            if ($result['status'] === 'no_changes') {
                $res->status(200)->json($result, $result['message']);
                return;
            }

            $res->status(200)->json($result, $result['message']);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");

        } catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            // Eliminar espacio
            $this->service->deleteEspacio($id);

            $res->status(200)->json([], "Espacio eliminado correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");

        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    // Métodos adicionales específicos para espacios

    public function findByEdificio(Request $req, Response $res, string $idEdificio): void
    {
        try {
            // Validar que el ID del edificio es numérico
            if (!is_numeric($idEdificio)) {
                $res->status(400)->json([], "ID de edificio inválido");
                return;
            }

            $espacios = $this->service->getEspaciosByEdificio((int) $idEdificio);
            $res->status(200)->json($espacios);

        } catch (Throwable $e) {
            $code = $e->getCode() ?: 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    public function verificarDisponibilidad(Request $req, Response $res, string $id): void
{
    try {
        // Intentar obtener de JSON primero, luego de query params
        $datos = $req->json();
        $inicio = $datos['inicio'] ?? $req->getParam('inicio');
        $fin = $datos['fin'] ?? $req->getParam('fin');

        // Si no vienen en JSON, intentar del body
        if (!$inicio || !$fin) {
            $body = $req->getBody();
            $inicio = $body['inicio'] ?? null;
            $fin = $body['fin'] ?? null;
        }

        if (!$inicio || !$fin) {
            $res->status(400)->json([], "Se requieren las fechas de inicio y fin. Envía en JSON: {'inicio': '...', 'fin': '...'} o como query params: ?inicio=...&fin=...");
            return;
        }

        $resultado = $this->service->verificarDisponibilidad($id, $inicio, $fin);
        
        $mensaje = $resultado['disponible'] 
            ? "El espacio está disponible en el horario solicitado" 
            : "El espacio no está disponible en el horario solicitado";
        
        $res->status(200)->json($resultado, $mensaje);
        
    } catch (ValidationException $e) {
        $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        
    } catch (Throwable $e) {
        $code = $e->getCode() ?: 500;
        $res->errorJson($e->getMessage(), $code);
    }
}

    // Método para obtener espacios disponibles (con filtros opcionales)
    public function disponibles(Request $req, Response $res): void
    {
        try {
            // Obtener filtros del query string
            $filtros = [
                'edificio' => $req->getParam('edificio'),
                'planta' => $req->getParam('planta'),
                'es_aula' => $req->getParam('es_aula'),
                'fecha_inicio' => $req->getParam('fecha_inicio'),
                'fecha_fin' => $req->getParam('fecha_fin')
            ];

            // Por ahora, obtenemos todos los espacios
            // En una implementación completa, se filtrarían aquí
            $espacios = $this->service->getAllEspacios();

            $res->status(200)->json([
                'espacios' => $espacios,
                'filtros_aplicados' => array_filter($filtros)
            ]);

        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}