<?php
declare(strict_types=1);

// Controllers/UsuarioController.php

// Esta pagina se encarga de recibir la peticion HTTP, llamar al servicio y devolver una respuesta HHTTP compuesta por el status y el JSON


namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;

use Services\IncidenciaService;


class IncidenciaController
{
    private IncidenciaService $service;

    public function __construct()
    {
        $this->service = new IncidenciaService();
    }

    
    /**
     * GET /incidencias
     * aqui se ejecuta la funcion getallincidencias el objeto service que se acaba de crear y la respuesta con el codigo de estado y transformando el array de profesores a json
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $incidencias = $this->service->getAllIncidencias();
            $res->status(200)->json($incidencias);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }


    /**
     * POST /incidencias
     * llama al servicio, formatea la respuesta para devolverla y capturar excepciones
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $result = $this->service->createIncidencia($req->json());

            $res->status(201)->json(
                ['id' => $result['id']],
                "Incidencia creado correctamente"
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


    /**
     * PUT /incidencias/{id}
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            //tras recibir los datos de id y del request llama al service
            $result = $this->service->updateIncidencia((int)$id, $req->json());
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


    /**
     * DELETE /incidencias/{id}
     */
    public function destroy(Request $req, Response $res, string $id): void
    {
        try {
            $id = (int) $id; //se convierte el id a entero

            $service = new \Services\IncidenciaService();
            $service->deleteIncidencia($id); //llama al servicio

            $res->status(200)->json([], "Incidencia eliminado correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }
}
