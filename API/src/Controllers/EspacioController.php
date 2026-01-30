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
        }
        catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        }
        catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * Eliminar espacio
     */
    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            $this->service->deleteEspacio($id);
            
            $res->status(200)->json([], "Espacio eliminado correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
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
     * Obtener espacios activos
     */
    public function getActivos(Request $req, Response $res): void
    {
        try {
            $espacios = $this->service->getEspaciosActivos();
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Cambiar estado de un espacio (activar/desactivar)
     */
    public function toggleEstado(Request $req, Response $res, string $id): void
    {
        try {
            // Obtener estado del body (por defecto es toggle si no se envía)
            $body = $req->json();
            $estado = isset($body['estado']) ? (int) $body['estado'] : null;
            
            // Si no se envía estado, hacer toggle basado en estado actual
            if ($estado === null) {
                $espacio = $this->service->getEspacioById($id);
                $estado = $espacio['activo'] == 1 ? 0 : 1;
            }

            $result = $this->service->toggleEstadoEspacio($id, $estado);
            $res->status(200)->json([], $result['message']);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() > 0 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * Obtener características de un espacio
     */
    public function getCaracteristicas(Request $req, Response $res, string $id): void
    {
        try {
            $caracteristicas = $this->service->getCaracteristicasEspacio($id);
            $res->status(200)->json($caracteristicas);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
            return;
        } catch (Throwable $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            $res->errorJson($e->getMessage(), $status);
        }
    }

    /**
     * Buscar espacios por características
     */
    public function searchByCaracteristicas(Request $req, Response $res): void
    {
        try {
            $resultados = $this->service->searchEspaciosByCaracteristicas($req->json());
            $res->status(200)->json($resultados);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
            return;
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Opcional: Método para obtener espacios con filtros avanzados
     */
    public function search(Request $req, Response $res): void
    {
        try {
            $filters = $req->query();
            $resultados = [];
            
            // Filtro por edificio
            if (isset($filters['edificio_id'])) {
                $resultados = $this->service->getEspaciosByEdificio((int) $filters['edificio_id']);
            }
            // Filtro por estado
            elseif (isset($filters['activo']) && $filters['activo'] == '1') {
                $resultados = $this->service->getEspaciosActivos();
            }
            // Sin filtros, obtener todos
            else {
                $resultados = $this->service->getAllEspacios();
            }
            
            $res->status(200)->json($resultados);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
            return;
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}