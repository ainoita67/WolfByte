<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\EspacioService;
use Services\LogAccionesService;

class EspacioController
{
    private EspacioService $service;
    private LogAccionesService $serviceLog;

    public function __construct()
    {
        $this->service = new EspacioService();
        $this->serviceLog = new LogAccionesService();
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
            $data=$req->json();

            $log['id_usuario_actor']=(int)$data['id_usuario'];
            
            // Crear nuevo espacio
            $result = $this->service->createEspacio($data);
            
            $log['id_recurso']=$result['id_recurso'];

            $this->serviceLog->createLog('Creación de espacio', $log);

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

    public function update(Request $req, Response $res): void
    {
        try {
            $data=$req->json();

            $log['id_usuario_actor']=(int)$data['id_usuario'];
            
            // Crear nuevo espacio
            $result = $this->service->updateEspacio($data);
            
            if($result['status']=='no_changes'){
                $res->status(200)->json([
                    "status" => "no_changes",
                    "message" => "No han habido cambios"
                ]);
            }else{
                $log['id_recurso']=$result['data']['id_recurso'];

                $this->serviceLog->createLog('Modificación de espacio', $log);

                $res->status(201)->json(
                    $result,
                    "Espacio actualizado correctamente"
                );
            }

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

    public function indexOtrosEspacios(Request $req, Response $res): void
    {
        try {
            // Obtener todos los espacios que son aulas
            $espacios = $this->service->getOtrosEspacios();
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function indexOtrosEspaciosDisponibles(Request $req, Response $res): void
    {
        try {
            // Obtener todos los espacios que son aulas
            $espacios = $this->service->getOtrosEspaciosDisponibles($req->json());
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}