<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaEspacioModel;
use Validation\ValidationException;
use Validation\Validator;

class ReservaEspacioService
{
    private ReservaEspacioModel $model;

    public function __construct()
    {
        $this->model = new ReservaEspacioModel();
    }

    public function getAllReservas(): array
    {
        return $this->model->getAll();
    }

    public function getMisReservas(int $idUsuario): array
    {
        if ($idUsuario <= 0) {
            throw new ValidationException(["Usuario no válido"]);
        }
        return $this->model->getByUsuario($idUsuario);
    }

    public function getReservasByEspacio(string $idEspacio): array
    {
        if (empty($idEspacio)) {
            throw new ValidationException(["ID de espacio es requerido"]);
        }
        return $this->model->getByEspacio($idEspacio);
    }

    public function getReservaById(int $id): array
    {
        $reserva = $this->model->findById($id);
        if (!$reserva) {
            throw new ValidationException(["Reserva no encontrada"]);
        }
        return $reserva;
    }

    // API/src/Services/ReservaEspacioService.php - Método createReservaEspacio()

    public function createReservaEspacio(array $data): array
    {
        // Validar datos de entrada
        if($data['autorizada'] == "true" || $data['autorizada'] =="1" || $data['autorizada'] ==1) {
            $data['autorizada'] = 1;
        } else{
            $data['autorizada'] = 0;
        }
        $validatedData = Validator::validate($data, [
            'asignatura' => 'required|string|min:1|max:100',
            'autorizada' => 'required|in:0,1',
            'observaciones' => 'string',
            'grupo' => 'required|string|min:1|max:50',
            'profesor' => 'required|string|min:1|max:100',
            'inicio' => 'required|string',
            'fin' => 'required|string',
            'id_usuario' => 'required|int|min:1',
            'id_espacio' => 'required|string|min:1|max:10',
            'actividad' => 'string'
        ]);

        // Establecer valores por defecto
        $validatedData['autorizada'] = $validatedData['autorizada'] ?? 0;
        $validatedData['observaciones'] = $validatedData['observaciones'] ?? null;
        $validatedData['actividad'] = $validatedData['actividad'] ?? null;

        // Validaciones adicionales
        $errors = [];

        // Verificar que la fecha de inicio sea anterior a la de fin
        $inicio = strtotime($validatedData['inicio']);
        $fin = strtotime($validatedData['fin']);
        
        if ($inicio === false || $fin === false) {
            $errors[] = "Fechas inválidas";
        } elseif ($inicio >= $fin) {
            $errors[] = "La fecha de inicio debe ser anterior a la fecha de fin";
        }

        // Verificar que la reserva no sea en el pasado
        if ($inicio < time()) {
            $errors[] = "No se pueden hacer reservas en fechas pasadas";
        }

        // Verificar que la reserva no exceda las 24 horas
        if ($inicio && $fin) {
            $duracionHoras = ($fin - $inicio) / 3600;
            if ($duracionHoras > 24) {
                $errors[] = "La reserva no puede exceder las 24 horas";
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        
        return $this->model->create($validatedData);
    }

    public function updateReservaEspacio(int $id, array $data): array
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        // Validar datos de entrada
        $validatedData = Validator::validate($data, [
            'asignatura' => 'required|string|min:1|max:100',
            'grupo' => 'required|string|min:1|max:50',
            'profesor' => 'required|string|min:1|max:100',
            'inicio' => 'required|string',
            'fin' => 'required|string',
            'id_usuario' => 'required|int|min:1',
            'id_espacio' => 'required|string|min:1|max:10',
            'autorizada' => 'int|in:0,1',
            'observaciones' => 'string',
            'actividad' => 'string'
        ]);

        // Validaciones adicionales
        $errors = [];

        // Verificar que la fecha de inicio sea anterior a la de fin
        $inicio = strtotime($validatedData['inicio']);
        $fin = strtotime($validatedData['fin']);
        
        if ($inicio >= $fin) {
            $errors[] = "La fecha de inicio debe ser anterior a la fecha de fin";
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->model->update($id, $validatedData);
    }

    public function cambiarFechasReserva(int $id, array $data): array
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        // Validar datos de entrada
        $validatedData = Validator::validate($data, [
            'inicio' => 'required|string',
            'fin' => 'required|string'
        ]);

        // Validaciones adicionales
        $errors = [];

        // Verificar que la fecha de inicio sea anterior a la de fin
        $inicio = strtotime($validatedData['inicio']);
        $fin = strtotime($validatedData['fin']);
        
        if ($inicio >= $fin) {
            $errors[] = "La fecha de inicio debe ser anterior a la fecha de fin";
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return $this->model->cambiarFechas($id, $validatedData);
    }

    public function deleteReservaEspacio(int $id): void
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        // Verificar que la reserva existe
        $reserva = $this->model->findById($id);
        if (!$reserva) {
            throw new ValidationException(["Reserva no encontrada"]);
        }

        $this->model->delete($id);
    }
}