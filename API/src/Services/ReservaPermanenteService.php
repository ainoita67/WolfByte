<?php
declare(strict_types=1);

namespace Services;

use Core\Request;
use Core\Response;
use Models\ReservaPermanenteModel;
use Models\PortatilModel;
use Throwable;
use Validation\Validator;
use Validation\ValidationException;

class ReservaPermanenteService
{
    private ReservaPermanenteModel $model;
    private PortatilModel $portatilModel;

    public function __construct()
    {
        $this->model = new ReservaPermanenteModel();
        $this->portatilModel = new PortatilModel();
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

        $data = Validator::validate($input, [
            'inicio'        => 'required|string',
            'fin'           => 'required|string',
            'comentario'    => 'string',
            'id_recurso'       => 'required|string',
            'dia_semana'    => 'required|int|min:0|max:7',
            'unidades'      => 'int'
        ]);

        try {
            $id = $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$id) {
            throw new \Exception("No se pudo crear la reserva permanente");
        }

        return ['id' => $id];
    }

    /**
     * Actualizar reserva permanente
     */
    public function updateReservaPermanente(int $id, array $input): array
    {
        $id = Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'inicio'        => 'required',
            'fin'           => 'required',
            'comentario'    => 'string',
            'recurso'    => 'required|string',
            'unidades'      => 'int',
            'dia_semana'    => 'required|int|min:0|max:7',
        ]);

        // maximo de unidades posibles
        $unidades_maximas = $this->portatilModel->getMaterialUnidades($data['recurso']);

        // unidades reservadas para ese recurso en ese dia y hora
        $unidades_reservadas = $this->model->unidadesReservadas(
            $data['recurso'], 
            $data['dia_semana'], 
            $data['inicio'], 
            $data['fin']
        );
        if ($unidades_reservadas + $data['unidades'] > $unidades_maximas) {
            throw new \Exception("No hay suficientes unidades disponibles. Unidades solicitadas: " . $data['unidades'] . ", Unidades totales: " . $unidades_maximas . ", Unidades libres: " . ($unidades_maximas - $unidades_reservadas), 500);
        }else{
            try {
                $result = $this->model->update($id['id'], $data);
            } catch (Throwable $e) {
                throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
            }

            if (!$result) {
                return [
                    'status'  => 'no_changes',
                    'message' => 'No hubo cambios en la reserva permanente'
                ];
            }

            return [
                'status'  => 'updated',
                'message' => 'Reserva permanente actualizada correctamente'
            ];
        }
    }

 /**
     * Cambiar estado activo/inactivo
     */
    public function toggleActiveStatus(int $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            $isActive = $this->model->isActive($id);
            if (!$isActive) {
                $result = $this->model->setActive($id);
            } else {
                $result = $this->model->setInactive($id);
            }
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$result) {
            $this->ensureUserExists($id);
            return [
                'status'  => 'no_changes',
                'message' => 'No hubo cambios en el estado de la reserva permanente'
            ];
        }

        return [
            'status'  => 'updated',
            'message' => 'Estado de la reserva permanente actualizado correctamente'
        ];
    }

    /**
     * Desactivar todas las reservas permanentes
     */
    public function desactivarTodo(): array
    {
        try {
            $result = $this->model->desactivarTodo();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$result) {
            $this->ensureUserExists($id);
            return [
                'status'  => 'no_changes',
                'message' => 'No hubo cambios en el estado de las reservas permanentes'
            ];
        }

        return [
            'status'  => 'updated',
            'message' => 'Estado de las reservas permanentes actualizado correctamente'
        ];
    }    
}