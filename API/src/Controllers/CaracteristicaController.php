<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\CaracteristicaService;

class CaracteristicaController{

    private CaracteristicaService $service;

    public function __construct()
    {
        $this->service = new CaracteristicaService();
    }

    public function index(Request $req, Response $res): void
    {
        try {
            $caracteristicas = $this->service->getAllCaracteristicas();
            $res->status(200)->json($caracteristicas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $caracteristica = $this->service->getCaracteristicaById((int) $id);
            $res->status(200)->json($caracteristica);

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
            $result = $this->service->createCaracteristica($req->json());

            $res->status(201)->json(
                ['id' => $result['id']],
                "Caracteristica creada correctamente"
            );

        } catch (ValidationException $e) {

            $res->status(422)->json(
                ['errors' => $e->errors],
                "Errores de validación"
            );
            return;

        } catch (Throwable $e) {

            $res->errorJson(app_debug() ? $e->getMessage() : "Error interno del servidor",500);
            return;
        }
    }
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            //tras recibir los datos de id y del request llama al service
            $result = $this->service->updateCaracteristica((int)$id, $req->json());
            //depende de lo recibido del servicio envia no_changes o updated
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

    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            $id = (int) $id; //se convierte el id a entero

            $service = new CaracteristicaService();
            $service->deleteCaracteristica($id); //llama al servicio

            $res->status(200)->json([], "Caracteristica eliminada correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }

}