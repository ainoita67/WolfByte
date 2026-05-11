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
        if($recurso['tipo']=="Espacio"&&(!$data['unidades']||$data['unidades']<=0)){
            $data['unidades']==null;
        }
        if($recurso['tipo']=="Espacio"&&$data['unidades']>0){
            throw new \Exception("Los espacios no pueden tener unidades");
        }
        if($recurso['tipo']=="Material"&&(!$data['unidades']||$data['unidades']==null||$data['unidades']<=0)){
            throw new \Exception("Los materiales deben tener unidades");
        }

        $mensaje="<ul class='m-0'><li>Recurso: ".$data['id_recurso']."</li><li>Inicio: ".$data['inicio']."</li><li>Fin: ".$data['fin']."</li></ul>";
        if($recurso['tipo']=="Espacio"){
            if(!$this->model->getEspacioFecha($data, 0)){
                
                throw new \Exception("El espacio ya está reservado entre ese horario<br>".$mensaje);
            }
        }
        if($recurso['tipo']=="Material"){
            if($data['unidades']>$this->model->getUnidadesFecha($data, 0)){
                throw new \Exception("No hay suficientes unidades entre ese horario<br>".$mensaje);
            }
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
        if($recurso['tipo']=="Espacio"&&(!$data['unidades']||$data['unidades']<=0)){
            $data['unidades']==null;
        }
        if($recurso['tipo']=="Espacio"&&$data['unidades']>0){
            throw new \Exception("Los espacios no pueden tener unidades");
        }
        if($recurso['tipo']=="Material"&&(!$data['unidades']||$data['unidades']==null||$data['unidades']<=0)){
            throw new \Exception("Los materiales deben tener unidades");
        }
        
        $mensaje="<ul class='m-0'><li>Recurso: ".$data['id_recurso']."</li><li>Inicio: ".$data['inicio']."</li><li>Fin: ".$data['fin']."</li></ul>";
        if($recurso['tipo']=="Espacio"){
            if(!$this->model->getEspacioFecha($data, (int)$idreserva['id'])){
                throw new \Exception("El espacio ya está reservado entre ese horario<br>".$mensaje);
            }
        }
        if($recurso['tipo']=="Material"){
            if($data['unidades']>$this->model->getUnidadesFecha($data, (int)$idreserva['id'])){
                throw new \Exception("No hay suficientes unidades entre ese horario<br>".$mensaje);
            }
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
        $reserva=$this->getReservaPermanenteById($id);

        $recurso=$this->serviceRecurso->getRecursoById($reserva['id_recurso']);
        if($recurso['tipo']=="Espacio"&&(!$reserva['unidades']||$reserva['unidades']<=0)){
            $reserva['unidades']==null;
        }
        if($recurso['tipo']=="Espacio"&&$reserva['unidades']>0){
            throw new \Exception("Los espacios no pueden tener unidades");
        }
        if($recurso['tipo']=="Material"&&(!$reserva['unidades']||$reserva['unidades']==null||$reserva['unidades']<=0)){
            throw new \Exception("Los materiales deben tener unidades");
        }

        $mensaje="<ul class='m-0'><li>Recurso: ".$reserva['id_recurso']."</li><li>Inicio: ".$reserva['inicio']."</li><li>Fin: ".$reserva['fin']."</li></ul>";
        if($recurso['tipo']=="Espacio"){
            if(!$this->model->getEspacioFecha($reserva, (int)$data['id'])){
                throw new \Exception("El espacio ya está reservado entre ese horario<br>".$mensaje);
            }
        }
        if($recurso['tipo']=="Material"){
            if($reserva['unidades']>$this->model->getUnidadesFecha($reserva, (int)$data['id'])){
                $mensaje="<ul class='m-0'><li>Recurso: ".$reserva['id_recurso']."</li><li>Inicio: ".$reserva['inicio']."</li><li>Fin: ".$reserva['fin']."</li></ul>";
                throw new \Exception("No hay suficientes unidades entre ese horario<br>".$mensaje);
            }
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