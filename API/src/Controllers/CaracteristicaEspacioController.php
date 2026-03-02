<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Models\CaracteristicaEspacioModel;
use Models\EspacioModel;
use Models\CaracteristicaModel;

class CaracteristicaEspacioController
{
    private CaracteristicaEspacioModel $model;
    private EspacioModel $espacioModel;
    private CaracteristicaModel $caracteristicaModel;

    public function __construct()
    {
        $this->model = new CaracteristicaEspacioModel();
        $this->espacioModel = new EspacioModel();
        $this->caracteristicaModel = new CaracteristicaModel();
    }

    /**
     * GET /caracteristicasEspacios
     * Listar todas las características de espacios (relaciones)
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $relaciones = $this->model->getAll();
            $res->status(200)->json($relaciones);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /espacios/{id}/caracteristicas
     * Listar características de un espacio específico
     */
    public function showByEspacio(Request $req, Response $res, string $id): void
    {
        try {
            // Verificar que el espacio existe
            $espacio = $this->espacioModel->findById($id);
            if (!$espacio) {
                throw new \Exception("Espacio no encontrado", 404);
            }

            $caracteristicas = $this->model->getByEspacio($id);
            $res->status(200)->json($caracteristicas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * GET /espacios/{id}/caracteristicas/disponibles
     * Listar características disponibles para un espacio específico
     */
    public function showDisponibles(Request $req, Response $res, string $id): void
    {
        try {
            // Verificar que el espacio existe
            $espacio = $this->espacioModel->findById($id);
            if (!$espacio) {
                throw new \Exception("Espacio no encontrado", 404);
            }

            $disponibles = $this->model->getDisponiblesParaEspacio($id);
            $res->status(200)->json($disponibles);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * POST /espacios/{id}/caracteristicas
     * Asignar una característica a un espacio
     */
    public function asignar(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json();
            
            // Validar datos
            if (!isset($data['id_caracteristica']) || !is_numeric($data['id_caracteristica'])) {
                throw new ValidationException(['id_caracteristica' => 'ID de característica requerido'], 422);
            }

            // Verificar que el espacio existe
            $espacio = $this->espacioModel->findById($id);
            if (!$espacio) {
                throw new \Exception("Espacio no encontrado", 404);
            }

            // Verificar que la característica existe
            $caracteristica = $this->caracteristicaModel->findById((int)$data['id_caracteristica']);
            if (!$caracteristica) {
                throw new \Exception("Característica no encontrada", 404);
            }

            // Asignar característica al espacio
            $resultado = $this->model->asignarAEspacio($id, (int)$data['id_caracteristica']);

            if (!$resultado) {
                $res->status(409)->json([], "La característica ya está asignada a este espacio");
                return;
            }

            $res->status(201)->json(
                $caracteristica,
                "Característica asignada correctamente al espacio"
            );

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

    /**
     * DELETE /espacios/{id}/caracteristicas
     * Quitar una característica de un espacio
     */
    public function quitar(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json();
            
            // Validar datos
            if (!isset($data['id_caracteristica']) || !is_numeric($data['id_caracteristica'])) {
                throw new ValidationException(['id_caracteristica' => 'ID de característica requerido'], 422);
            }

            // Quitar característica del espacio
            $resultado = $this->model->quitarDeEspacio($id, (int)$data['id_caracteristica']);

            if (!$resultado) {
                $res->status(404)->json([], "La asignación no existe");
                return;
            }

            $res->status(200)->json([], "Característica quitada correctamente del espacio");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }
}