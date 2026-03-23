<?php
declare(strict_types=1);

// Controllers/IncidenciaController.php

// Esta pagina se encarga de recibir la peticion HTTP, llamar al servicio y devolver una respuesta HHTTP compuesta por el status y el JSON


namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;

use Services\IncidenciaService;
use Services\LogAccionesService;


class IncidenciaController
{
    private IncidenciaService $service;
    private LogAccionesService $serviceLog;

    public function __construct()
    {
        $this->service = new IncidenciaService();
        $this->serviceLog = new LogAccionesService();
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
     * GET /incidencia/usuario/{id_usuario}
     * Devuelve una incidencia por ID de usuario
     */
    public function showByUsuario(Request $req, Response $res, string $id_usuario): void
    {
        try {
            $incidencia = $this->service->getIncidenciasByUsuario($id_usuario);
            $res->status(200)->json($incidencia);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }


    /**
     * POST /incidencias
     * llama al servicio, formatea la respuesta para devolverla y capturar excepciones
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data=$req->json();
            $result = $this->service->createIncidencia($data);
            $log['id_incidencia']=(int)$result['id'];
            $log['id_usuario_actor']=(int)$data['id_usuario'];
            $this->serviceLog->createLog('Creación de incidencia', $log);

            $res->status(201)->json(
                ['id' => $result['id']],
                "Incidencia creada correctamente"
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
            $data=$req->json();
            $result = $this->service->updateIncidencia((int)$id, $data);
            //depende de lo recibido del servicio envia no_changes o updated
            if ($result['status'] === 'no_changes') {
                $res->status(200)->json([], $result['message']);
                return;
            }else{
                $log['id_incidencia']=(int)$id;
                $log['id_usuario_actor']=(int)$data['id_usuario'];
                $this->serviceLog->createLog('Modificación de incidencia', $log);
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
            $data->$req->json();
            $log['id_incidencia']=(int)$id;
            $log['id_usuario_actor']=(int)$data['id_usuario'];

            $this->$service->deleteIncidencia((int)$id); //llama al servicio
            $this->serviceLog->createLog('Resolución de incidencia', $log);

            $res->status(200)->json([], "Incidencia eliminada correctamente");

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
        } catch (Throwable $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            $res->errorJson($e->getMessage(), $code);
        }
    }
}
