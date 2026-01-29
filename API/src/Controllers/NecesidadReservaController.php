<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\NecesidadReservaService;
use Throwable;
use Validation\ValidationException;

class NecesidadReservaController
{
    private NecesidadReservaService $service;

    public function __construct()
    {
        $this->service = new NecesidadReservaService();
    }

    /**
     * Asignar una necesidad a una reserva-espacio
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->json();
            $idReservaEspacio = (int)$req->param('id_reserva_espacio');

            if (!isset($data['id_necesidad'])) {
                $res->status(422)->json(['errors' => ['id_necesidad' => 'Campo requerido']]);
                return;
            }

            $relacion = $this->service->create($idReservaEspacio, (int)$data['id_necesidad']);

            $res->status(201)->json(['data' => $relacion], "Necesidad asignada correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * Listar necesidades de una reserva
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $idReserva = (int)$req->param('id_reserva');
            $data = $this->service->getByReserva($idReserva);

            $res->status(200)->json(['data' => $data], "Necesidades de la reserva");
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * Ver una necesidad asignada específica
     */
    public function show(Request $req, Response $res): void
    {
        $idReservaEspacio = (int)$req->param('id_reserva_espacio');
        $idNecesidad = (int)$req->param('id_necesidad');

        $data = $this->service->getOne($idReservaEspacio, $idNecesidad);

        if (!$data) {
            $res->status(404)->json([], "Relación no encontrada");
            return;
        }

        $res->status(200)->json(['data' => $data], "Relación encontrada");
    }

    /**
     * Cambiar una necesidad por otra
     */
    public function update(Request $req, Response $res): void
    {
        try {
            $data = $req->json();
            $idReservaEspacio = (int)$req->param('id_reserva_espacio');
            $idNecesidad = (int)$req->param('id_necesidad');

            if (!isset($data['id_necesidad_nueva'])) {
                $res->status(422)->json(['errors' => ['id_necesidad_nueva' => 'Campo requerido']]);
                return;
            }

            $updated = $this->service->update(
                $idReservaEspacio,
                $idNecesidad,
                (int)$data['id_necesidad_nueva']
            );

            $res->status(200)->json(['data' => $updated], "Necesidad actualizada");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * Quitar una necesidad de una reserva
     */
    public function destroy(Request $req, Response $res): void
    {
        try {
            $idReservaEspacio = (int)$req->param('id_reserva_espacio');
            $idNecesidad = (int)$req->param('id_necesidad');

            $this->service->delete($idReservaEspacio, $idNecesidad);

            $res->status(200)->json([], "Necesidad eliminada correctamente");

        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * Reemplazar todas las necesidades de una reserva
     */
    public function sync(Request $req, Response $res): void
    {
        try {
            $data = $req->json();
            $idReservaEspacio = (int)$req->param('id_reserva_espacio');

            if (!isset($data['necesidades']) || !is_array($data['necesidades'])) {
                $res->status(422)->json(['errors' => ['necesidades' => 'Debe ser un array']]);
                return;
            }

            $this->service->sync($idReservaEspacio, $data['necesidades']);

            $res->status(200)->json([], "Necesidades sincronizadas");

        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}
