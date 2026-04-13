<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Core\Session;
use Services\ReservaPermanenteService;
use Services\LogAccionesService;
use Throwable;
use Validation\ValidationException;

class ReservaPermanenteController
{
    private ReservaPermanenteService $service;
    private LogAccionesService $serviceLog;

    public function __construct()
    {
        $this->service = new ReservaPermanenteService();
        $this->serviceLog = new LogAccionesService();
    }

    /**
     * GET /reservas_permanentes
     * Devuelve todas las reservas permanentes activas
     */
    public function index(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservasPermanentes();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
    /**
     * GET /reservas_permanentes/inactivas
     * Devuelve todas las reservas permanentes inactivas
     */
    public function indexInactivas(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservasPermanentesInactivas();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
    /**
     * GET /reservas_permanentes/{id}
     * Devuelve una reserva permanente por ID
     */
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaPermanenteById($id);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * GET /reservas_permanentes/recurso/{id_recurso}
     * Muestra todas las reservas permanentes activas por recurso
     */
    public function showActivasRecurso(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaPermanenteRecurso($id);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 404);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * POST /reservas_permanentes
     * Crea una nueva reserva permanente
     */
    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $log['id_usuario_actor']=$data['id_usuario'];
            $reserva = $this->service->createReservaPermanente($data);
            $log['id_reserva_permanente']=$reserva['id_reserva_permanente'];
            $this->serviceLog->createLog("Creación de reserva permanente", $log);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PUT /reservas_permanentes/{id}
     * Modifica totalmente una reserva permanente por ID
     */
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $log['id_usuario_actor']=$data['id_usuario'];
            $reserva = $this->service->getReservaPermanenteById($id);
            $data['activo']=$reserva['activo'];
            $reserva = $this->service->updateReservaPermanente($id, $data);
            if($reserva['status']=="updated"){
                $log['id_reserva_permanente']=$reserva['data']['id_reserva_permanente'];
                $this->serviceLog->createLog("Modificación de reserva permanente", $log);
            }
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422, [
                'errors' => $e->getErrors()
            ]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    /**
     * PATCH /reservas_permanentes/{id}/activar
     * Activa una reserva permanente por ID
     */
    public function activate(Request $req, Response $res, string $id): void
    {
        try {
            $data=$req->getBody();
            $log['id_usuario_actor']=(int)$data['id_usuario'];
            $activar=false;
            $reserva = $this->service->getReservaPermanenteById($id);
            $log['id_reserva_permanente']=(int)$reserva['id_reserva_permanente'];
            if($reserva['activo']==0||$reserva['activo']=="0"||$reserva['activo']==false){
                $activar=true;
            }
            
            $result = $this->service->activarReservaPermanente($id, $activar);

            if($activar){
                $this->serviceLog->createLog("Activación de reserva permanente", $log);
            }else{
                $this->serviceLog->createLog("Desactivación de reserva permanente", $log);
            }

            $res->status(200)->json([], $result['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * PATCH /reservas_permanentes/{id}/desactivar_todo
     * Desactiva todas las reservas permanentes
     */
    public function deactivate(Request $req, Response $res): void
    {
        try {
            $data=$req->getBody();
            $log['id_usuario_actor']=(int)$data['id_usuario'];
            
            $reservas = $this->service->getAllReservasPermanentes();
            $reservasinactivas = $this->service->desactivarReservasPermanentes();
            foreach ($reservas as $reserva) {
                if(!(in_array($reserva['id_reserva_permanente'], array_column($reservasinactivas, 'id_reserva_permanente')))){
                    $log['id_reserva_permanente']=(int)$reserva['id_reserva_permanente'];
                    $this->serviceLog->createLog("Desactivación de reserva permanente", $log);
                }
            }

            $respuesta=[
                'status' => 'success',
                'message' => 'Todas las reservas permanentes han sido desactivadas correctamente',
                'data' => $reservasinactivas
            ];
            $res->status(201)->json($respuesta);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}