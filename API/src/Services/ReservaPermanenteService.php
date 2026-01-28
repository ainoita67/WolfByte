<?php
declare(strict_types=1);

namespace Services;

use Core\Request;
use Core\Response;
use Models\ReservaPermanenteModel;
use Throwable;
use Validation\Validator;
use Validation\ValidationException;

class ReservaPermanenteService
{
    private ReservaPermanenteModel $model;

    public function __construct()
    {
        $this->model = new ReservaPermanenteModel();
    }

    public function getAllReservasPermanentes(): array
    {
        return $this->model->getAll();
    }

    public function getReservaPermanenteById(string $id): array
    {
        $data = Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try{
            $ReservaPermanente = $this->model->findById($data['id']);
        }catch(Throwable $e) {
            throw new ValidationException("Reserva permanente no encontrada");
        }

        return $ReservaPermanente;
    }

    public function getReservaPermanenteByIdRecurso(string $id_recurso): array
    {
        $data = Validator::validate(['id_recurso' => $id_recurso], [
            'id_recurso' => 'required|string|min:1'
        ]);

        $ReservaPermanente = $this->model->findByIdRecurso($data['id_recurso']);

        if(!$ReservaPermanente){
            throw new ValidationException(["Recurso no encontrado"]);
        }

        return $ReservaPermanente;
    }

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
        ]);
        
        if (empty($data['id_recurso'])) {
            throw new ValidationException(array("id_recurso es obligatorio"));
        }

        return $this->model->create($data);
    }

    public function updateReservaPermanente(string $id, array $input): array
    {
        $id = (int)Validator::validate(['id' => $id], [
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
            'comentario'    => 'required|string',
            'activo'        => 'required|in:0,1',
            'id_recurso'    => 'required|string',
        ]);

        if (empty($data['id_recurso'])) {
            throw new ValidationException("id_recurso es obligatorio");
        }

        return $this->model->update($id, $data);
    }

    public function deleteReservaPermanente(int $id): void
    {
        $this->model->delete($id);
    }
}