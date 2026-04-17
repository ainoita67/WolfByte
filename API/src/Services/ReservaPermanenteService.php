<?php
declare(strict_types=1);

namespace Services;

use Core\Request;
use Core\Response;
use Models\ReservaPermanenteModel;
use Services\RecursoService;
use Throwable;
use Validation\Validator;
use Validation\ValidationException;

class ReservaPermanenteService
{
    private ReservaPermanenteModel $model;
    private RecursoService $serviceRecurso;

    public function __construct()
    {
        $this->model = new ReservaPermanenteModel();
        $this->serviceRecurso = new RecursoService();
    }

    /**
     * Obtener todas las reservas permanentes activas
     */
    public function getAllReservasPermanentes(): array
    {
        return $this->model->getAll();
    }

    /**
     * Obtener todas las reservas permanentes inactivas
     */
    public function getAllReservasPermanentesInactivas(): array
    {
        return $this->model->getAllInactivas();
    }

    /**
     * Obtener reserva permanente por ID
     */
    public function getReservaPermanenteById(string $id): array
    {
        $data = Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try{
            $ReservaPermanente = $this->model->findById($data['id']);
            return $ReservaPermanente;
        }catch(Throwable $e) {
            throw new ValidationException(["Reserva permanente no encontrada"]);
        }

        return ["mensaje" => "Reserva permanente no encontrada"];
    }

    /**
     * Obtener reserva permanente por ID de recurso
     */
    public function getReservaPermanenteRecurso(string $id_recurso): array
    {
        $data = Validator::validate(['id_recurso' => $id_recurso], [
            'id_recurso' => 'required|string|min:1'
        ]);

        try{
            $ReservaPermanente = $this->model->findByIdRecurso($data['id_recurso']);
            return $ReservaPermanente;
        }catch(Throwable $e) {
            throw new ValidationException(["Recurso no encontrado"]);
        }

        return ["mensaje" => "Recurso no encontrado"];
    }

    /**
     * Crear reserva permanente
     */
    public function createReservaPermanente(array $input): array
    {
        if($input['activo']=="true"||$input['activo']=="1"||$input['activo']==1){
            $input['activo']=1;
        }else{
            $input['activo']=0;
        }

        $data = Validator::validate($input, [
            'inicio'        => 'required|string',
            'fin'           => 'required|string',
            'comentario'    => 'string',
            'activo'        => 'required|in:0,1',
            'id_recurso'    => 'required|string',
            'dia_semana'    => 'required|int|min:1|max:5',
            'unidades'      => 'int|min:1'
        ]);

        if($data['comentario']){
            $data['comentario']=ucfirst(trim($data['comentario']));
        }

        $recurso=$this->serviceRecurso->getRecursoById($data['id_recurso']);
        if($recurso['tipo']=="Espacio"&&!$data['unidades']){
            $data['unidades']==null;
        }
        if($recurso['tipo']=="Espacio"&&$data['unidades']>0){
            throw new \Exception("Los espacios no pueden tener unidades");
        }
        if($recurso['tipo']=="Material"&&(!$data['unidades']||$data['unidades']==null)){
            throw new \Exception("Los materiales deben tener unidades");
        }
        
        if (empty($data['id_recurso'])) {
            throw new ValidationException(array("id_recurso es obligatorio"));
        }

        return $this->model->create($data);
    }

    /**
     * Actualizar reserva permanente
     */
    public function updateReservaPermanente(string $id, array $input): array
    {
        $idreserva = Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        if($input['activo']=="true"||$input['activo']=="1"||$input['activo']==1){
            $input['activo']=1;
        }else{
            $input['activo']=0;
        }

        $data = Validator::validate($input, [
            'inicio'        => 'required|string',
            'fin'           => 'required|string',
            'comentario'    => 'string',
            'activo'        => 'required|in:0,1',
            'id_recurso'    => 'required|string',
            'dia_semana'    => 'required|int|min:1|max:5',
            'unidades'      => 'int|min:1'
        ]);

        if($data['comentario']){
            $data['comentario']=ucfirst(trim($data['comentario']));
        }

        $recurso=$this->serviceRecurso->getRecursoById($data['id_recurso']);
        if($recurso['tipo']=="Espacio"&&!$data['unidades']){
            $data['unidades']==null;
        }
        if($recurso['tipo']=="Espacio"&&$data['unidades']>0){
            throw new \Exception("Los espacios no pueden tener unidades");
        }
        if($recurso['tipo']=="Material"&&(!$data['unidades']||$data['unidades']==null)){
            throw new \Exception("Los materiales deben tener unidades");
        }
        
        if (empty($data['id_recurso'])) {
            throw new \Exception("id_recurso es obligatorio");
        }

        if(!$this->model->update((int)$idreserva['id'], $data)){
            return[
                'status' => "no_changes",
                'message' => "No han habido cambios",
                'data' => $this->getReservaPermanenteById($id)
            ];
        }
        
        return[
            'status' => "updated",
            'message' => "Reserva actualizada correctamente",
            'data' => $this->getReservaPermanenteById($id)
        ];
    }

    /**
     * Activar reserva permanente
     */
    public function activarReservaPermanente(string $id, bool $activar): array
    {
        $data = Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        if(empty($data['id'])) {
            throw new ValidationException("ID es obligatorio");
        }

        if($activar){
            return [
                'message' => 'Reserva activada correctamente',
                'data' => $this->model->activar($data['id'])
            ];
        }else{
            return [
                'message' => 'Reserva desactivada correctamente',
                'data' => $this->model->desactivar($data['id'])
            ];
        }
    }

    /**
     * Desactivar todas las reservas permanentes
     */
    public function desactivarReservasPermanentes(): array
    {
        return $this->model->desactivartodas();
    }
}