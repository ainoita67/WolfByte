<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Services\ReservaEspacioService;
use Services\NecesidadReservaService;
use Services\LogAccionesService;
use Throwable;
use Validation\ValidationException;

class ReservaEspacioController
{
    private ReservaEspacioService $service;
    private NecesidadReservaService $serviceNecesidad;
    private LogAccionesService $serviceLog;

    public function __construct()
    {
        $this->service = new ReservaEspacioService();
        $this->serviceNecesidad = new NecesidadReservaService();
        $this->serviceLog = new LogAccionesService();
    }

    public function index(Request $req, Response $res): void
    {
        try {
            $reservas = $this->service->getAllReservas();
            $res->status(200)->json($reservas);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function showEspacio(Request $req, Response $res, string $id): void
    {
        try {
            $data = $this->service->getReservasPorEspacio($id);
            $res->status(200)->json($data);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $reserva = $this->service->getReservaById((int)$id);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->status(404)->json(['error' => $e->getMessage()]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    public function store(Request $req, Response $res): void
    {
        try {
            $data = $req->getBody();
            $log['id_usuario_actor']=$data['id_usuario'];
            $reserva = $this->service->createReserva($data);
            $log['id_reserva']=$reserva['id_reserva'];
            $this->serviceLog->createLog("Creación de reserva", $log);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        }
        catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }

    // Actualiza una reserva existente
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->getBody();
            $necesidadesantes=$this->serviceNecesidad->getNecesidadById((int)$id);
            
            if(!isset($data['necesidades'])||$data['necesidades']==null){
                $data['necesidades']=$necesidadesantes;
            }
            
            $this->serviceNecesidad->updateNecesidad((int)$id, $data);
            
            $necesidadesdespues=$this->serviceNecesidad->getNecesidadById((int)$id);
            
            $coincide=true;
            if(count($necesidadesantes)!=count($necesidadesdespues)){
                $coincide=false;
            }else{
                if(count($necesidadesdespues)!=0){
                    foreach ($necesidadesantes as $key => $antes) {
                        if ($antes !== $necesidadesdespues[$key]) {
                            $coincide = false;
                            break;
                        }
                    }
                }
            }

            if($coincide === false){
                $log['id_usuario_actor']=$data['id_usuario'];
                $log['id_reserva']=(int)$id;
                if($data['necesidades']==null||count($data['necesidades'])==0){
                    $this->serviceLog->createLog('Desvinculación de necesidades', $log);
                }else{
                    $this->serviceLog->createLog('Asignación de necesidades', $log);
                }
            }
            
            $reserva = $this->service->updateReserva((int)$id, $data);
            $res->status(200)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getErrors(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }
}