<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaPortatilModel;
use Services\ReservaService;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class ReservaPortatilService
{
    private ReservaPortatilModel $model;
    private ReservaService $serviceReserva;

    public function __construct()
    {
        $this->model = new ReservaPortatilModel();
        $this->serviceReserva = new ReservaService();
    }

    // Devuelve todas las reservas de portatils
    public function getAllReservas(): array
    {
        return $this->model->getAll();
    }

    // Devuelve todas las reservas de un portatil específico
    public function getReservasPorPortatil(string $idPortatil): array
    {
        Validator::validate(['id' => $idPortatil], [
            'id' => 'required|string|min:1'
        ]);

        try {
            $reservas = $this->model->getByPortatil($idPortatil);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$reservas) {
            throw new \Exception("reservas no encontrado", 404);
        }

        return $reservas;
    }

    // Devuelve una reserva por su ID
    public function getReservaById(int $id): array
    {
        $reserva = $this->model->findById($id);

        if (!$reserva) {
            throw new ValidationException([
                "Reserva ".$id => "Reserva no encontrada"
            ]);
        }

        return $reserva;
    }

    // Crea una nueva reserva
    public function createReserva(array $data): array
    {
        if(!$data['unidades']||$data['unidades']<=0){
            throw new \Exception("No se han reservado unidades");
        }
        
        if($this->model->getReservaFecha(-1, $data)){
            $this->validateReservaData($data, false);
            $reserva=$this->serviceReserva->createReserva($data);

            if (!$reserva||!$reserva['id_reserva']) {
                throw new \Exception("No se pudo crear la reserva");
            }

            $data['id_reserva_material']=(int)$reserva['id_reserva'];

            $reservaportatil=$this->model->create($data);

            if (!$reservaportatil||!$reservaportatil['id_reserva']) {
                throw new \Exception("No se pudo crear la reserva");
            }

            return [
                'id_reserva' => $reservaportatil['id_reserva'],
                'message' => 'Reserva creada correctamente'
            ];
        }
        throw new \Exception("No hay suficientes portátiles disponibles entre esas horas");
    }

    // Actualiza una reserva existente
    public function updateReserva(int $id, array $data): array
    {
        if($this->model->getReservaFecha($id, $data)&&count($this->model->findById($id))>0){
            $this->validateReservaData($data, false);
            $cambio=$this->serviceReserva->updateReserva($id, $data);
            if($this->model->update($id, $data)||$cambio['status']==='updated'){
                return [
                    'status'=>'updated',
                    'message'=>'Reserva actualizada correctamente',
                    'data'=>$this->model->findById($id)
                ];
            }

            return [
                'status'=>'no_changes',
                'message'=>'No han habido cambios',
                'data'=>$this->model->findById($id)
            ];
        }
        throw new \Exception("Ya hay una reserva entre esas horas");
    }

    // Valida los datos de la reserva
    private function validateReservaData(array $data, bool $isNew = true): void
    {
        $errors = [];

        if (empty($data['unidades']) || !is_numeric($data['unidades']) || $data['unidades'] <= 0) {
            $errors['unidades'] = "Las unidades son obligatorias y deben ser mayores que 0";
        }

        if (empty($data['usaenespacio'])) {
            $errors['usaenespacio'] = "El espacio de uso es obligatorio";
        }

        if (!isset($data['id_material']) || is_numeric($data['id_material'])) {
            $errors['id_material'] = "El ID del portátil es obligatorio y debe ser texto";
        }

        if (!isset($data['inicio']) || is_numeric($data['inicio'])) {
            $errors['inicio'] = "La fecha de inicio es obligatoria y debe ser texto";
        }

        if (!isset($data['fin']) || is_numeric($data['fin'])) {
            $errors['fin'] = "La fecha de fin es obligatoria y debe ser texto";
        }

        if (!isset($data['f_creacion']) || is_numeric($data['f_creacion'])) {
            $errors['f_creacion'] = "La fecha de fin es obligatoria y debe ser texto";
        }
        
        $inicio = date("Y-m-d H:i:s", strtotime($data['inicio']));
        $fin = date("Y-m-d H:i:s", strtotime($data['fin']));
        $creacion = date("Y-m-d H:i:s", strtotime($data['f_creacion']));

        if($inicio>=$fin){
            throw new \Exception("La fecha de inicio debe ser anterior a la fecha de fin");
        }

        if($creacion>$inicio){
            throw new \Exception("La fecha de creación no puede ser posterior a la fecha de inicio");
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}