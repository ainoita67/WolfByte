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

    /**
     * Obtener todos los espacios
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $espacios = $this->service->getAllEspacios();
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Obtener espacio por ID
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
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

    /**
     * Crear nuevo espacio
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $result = $this->service->createEspacio($req->json());

            $res->status(201)->json(
                ['id' => $result['id']],
                "Espacio creado correctamente"
            );

        } catch (ValidationException $e) {

            $res->status(422)->json(
                ['errors' => $e->errors],
                "Errores de validación"
            );
            return;

        } catch (Throwable $e) {

            $res->errorJson(app_debug() ? $e->getMessage() : "Error interno del servidor", 500);
            return;
        }
    }

    public function indexAulas(Request $req, Response $res): void
    {
        try {
            // Obtener todos los espacios que son aulas
            $espacios = $this->service->getAllAulas();
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function indexAulasDisponibles(Request $req, Response $res): void
    {
        try {
            // Obtener todos los espacios que son aulas
            $espacios = $this->service->getAulasDisponibles($req->json());
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    /**
     * Actualizar espacio
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $result = $this->service->updateEspacio($id, $req->json());

            if ($result['status'] === 'no_changes') {
                $res->status(200)->json([], $result['message']);
                return;
            }

            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * Obtener espacios por edificio
     */
    public function getByEdificio(Request $req, Response $res, string $idEdificio): void
    {
        try {
            $espacios = $this->service->getEspaciosByEdificio((int) $idEdificio);
            $res->status(200)->json($espacios);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
            return;
        } catch (Throwable $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            $res->errorJson($e->getMessage(), $status);
        }
    }

    /**
     * Obtener espacios libres entre dos fechas
     */
    public function getEspaciosLibres(Request $req, Response $res): void
    {
        try {
            // Obtener fechas de los query params
            $fechaInicio = $req->getParam('fecha_inicio');
            $fechaFin = $req->getParam('fecha_fin');

            if (!$fechaInicio || !$fechaFin) {
                throw new \Exception("Debe proporcionar fecha_inicio y fecha_fin como parámetros de consulta", 400);
            }

            $result = $this->service->getEspaciosLibresEntreFechas([
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ]);

            $res->status(200)->json($result, "Espacios libres encontrados");
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }
}