<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaPortatilModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class ReservaPortatilService
{
    private ReservaPortatilModel $model;

    public function __construct()
    {
        $this->model = new ReservaPortatilModel();
    }

    /**
     * Obtener todas las reservas de portátiles
     */
    public function getAllReservas(): array
    {
        try {
            return $this->model->getAll();
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener reservas de portátiles: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener reserva por ID
     */
    public function getReservaById(int $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            $reserva = $this->model->findById($id);
            
            if (!$reserva) {
                throw new \Exception("Reserva no encontrada", 404);
            }
            
            return $reserva;
            
        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener reservas por material (portátil)
     */
    public function getReservasByMaterial(string $idMaterial): array
    {
        Validator::validate(['id_material' => $idMaterial], [
            'id_material' => 'required|string|min:1|max:10'
        ]);

        try {
            return $this->model->findByMaterial($idMaterial);
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener reservas del material: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener reservas por usuario
     */
    public function getReservasByUsuario(int $idUsuario): array
    {
        Validator::validate(['id_usuario' => $idUsuario], [
            'id_usuario' => 'required|int|min:1'
        ]);

        try {
            return $this->model->findByUsuario($idUsuario);
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener reservas del usuario: " . $e->getMessage(), 500);
        }
    }

    /**
     * Verificar disponibilidad
     */
    public function checkDisponibilidad(array $input): array
    {
        $data = Validator::validate($input, [
            'carro' => 'required|string|min:1|max:10',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|string',
            'hora_fin' => 'required|string',
            'num_portatiles' => 'required|int|min:1',
            'excluir_id' => 'int|min:1'
        ]);

        $inicio = $data['fecha'] . ' ' . $data['hora_inicio'] . ':00';
        $fin = $data['fecha'] . ' ' . $data['hora_fin'] . ':00';

        // Validar que fin sea mayor que inicio
        if (strtotime($fin) <= strtotime($inicio)) {
            throw new ValidationException(["La hora fin debe ser posterior a la hora inicio"]);
        }

        try {
            // Obtener unidades totales del material
            $unidadesTotales = $this->model->getMaterialUnidades($data['carro']);
            
            if ($unidadesTotales === 0) {
                throw new \Exception("Carro no encontrado o sin portátiles disponibles", 404);
            }

            // Verificar disponibilidad
            $unidadesReservadas = $this->model->checkDisponibilidad(
                $data['carro'],
                $inicio,
                $fin,
                $data['num_portatiles'],
                $data['excluir_id'] ?? null
            );

            $unidadesDisponibles = $unidadesTotales - $unidadesReservadas;
            $disponible = $unidadesDisponibles >= $data['num_portatiles'];

            return [
                'disponible' => $disponible,
                'unidades_totales' => $unidadesTotales,
                'unidades_reservadas' => $unidadesReservadas,
                'unidades_disponibles' => $unidadesDisponibles,
                'unidades_solicitadas' => $data['num_portatiles']
            ];

        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al verificar disponibilidad: " . $e->getMessage(), 500);
        }
    }

    /**
     * Crear nueva reserva
     */
    public function createReserva(array $input): array
    {
        $data = Validator::validate($input, [
            'profesor' => 'required|string|min:1|max:100',
            'aula' => 'required|string|min:1|max:100',
            'num_portatiles' => 'required|int|min:1',
            'carro' => 'required|string|min:1|max:10',
            'edificio' => 'required|int|min:1',
            'planta' => 'required|int|min:0',
            'espacio' => 'required|string|min:1|max:10',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|string',
            'hora_fin' => 'required|string',
            'grupo' => 'required|string|min:1|max:50',
            'id_usuario' => 'required|int|min:1',
            'autorizada' => 'boolean'
        ]);

        $inicio = $data['fecha'] . ' ' . $data['hora_inicio'] . ':00';
        $fin = $data['fecha'] . ' ' . $data['hora_fin'] . ':00';

        // Validar que fin sea mayor que inicio
        if (strtotime($fin) <= strtotime($inicio)) {
            throw new ValidationException(["La hora fin debe ser posterior a la hora inicio"]);
        }

        // Verificar disponibilidad
        $disponibilidad = $this->checkDisponibilidad([
            'carro' => $data['carro'],
            'fecha' => $data['fecha'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'num_portatiles' => $data['num_portatiles']
        ]);

        if (!$disponibilidad['disponible']) {
            throw new ValidationException([
                "No hay suficientes portátiles disponibles. " .
                "Disponibles: {$disponibilidad['unidades_disponibles']}, " .
                "Solicitados: {$data['num_portatiles']}"
            ]);
        }

        // Valor por defecto para autorizada
        $data['autorizada'] = $data['autorizada'] ?? 0;

        try {
            $id = $this->model->create($data);
            
            if (!$id) {
                throw new \Exception("No se pudo crear la reserva");
            }

            return [
                'id' => $id,
                'message' => 'Reserva creada correctamente'
            ];

        } catch (Throwable $e) {
            throw new \Exception("Error al crear la reserva: " . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar reserva completa
     */
    public function updateReserva(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'profesor' => 'required|string|min:1|max:100',
            'aula' => 'required|string|min:1|max:100',
            'num_portatiles' => 'required|int|min:1',
            'carro' => 'required|string|min:1|max:10',
            'espacio' => 'required|string|min:1|max:10',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|string',
            'hora_fin' => 'required|string',
            'grupo' => 'required|string|min:1|max:50',
            'autorizada' => 'boolean'
        ]);

        $inicio = $data['fecha'] . ' ' . $data['hora_inicio'] . ':00';
        $fin = $data['fecha'] . ' ' . $data['hora_fin'] . ':00';

        // Validar que fin sea mayor que inicio
        if (strtotime($fin) <= strtotime($inicio)) {
            throw new ValidationException(["La hora fin debe ser posterior a la hora inicio"]);
        }

        // Valor por defecto para autorizada
        $data['autorizada'] = $data['autorizada'] ?? 0;

        try {
            // Verificar que la reserva existe
            $reservaExistente = $this->getReservaById($id);

            // Verificar disponibilidad excluyendo esta reserva
            $disponibilidad = $this->checkDisponibilidad([
                'carro' => $data['carro'],
                'fecha' => $data['fecha'],
                'hora_inicio' => $data['hora_inicio'],
                'hora_fin' => $data['hora_fin'],
                'num_portatiles' => $data['num_portatiles'],
                'excluir_id' => $id
            ]);

            if (!$disponibilidad['disponible']) {
                throw new ValidationException([
                    "No hay suficientes portátiles disponibles. " .
                    "Disponibles: {$disponibilidad['unidades_disponibles']}, " .
                    "Solicitados: {$data['num_portatiles']}"
                ]);
            }

            $result = $this->model->update($id, $data);

            if (!$result) {
                return [
                    'status' => 'no_changes',
                    'message' => 'No hubo cambios en la reserva'
                ];
            }

            return [
                'status' => 'updated',
                'message' => 'Reserva actualizada correctamente'
            ];

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al actualizar la reserva: " . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar solo fechas
     */
    public function updateFechas(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'fecha' => 'required|date',
            'hora_inicio' => 'required|string',
            'hora_fin' => 'required|string'
        ]);

        $inicio = $data['fecha'] . ' ' . $data['hora_inicio'] . ':00';
        $fin = $data['fecha'] . ' ' . $data['hora_fin'] . ':00';

        // Validar que fin sea mayor que inicio
        if (strtotime($fin) <= strtotime($inicio)) {
            throw new ValidationException(["La hora fin debe ser posterior a la hora inicio"]);
        }

        try {
            // Obtener reserva actual
            $reservaActual = $this->getReservaById($id);

            // Verificar disponibilidad para las nuevas fechas
            $disponibilidad = $this->checkDisponibilidad([
                'carro' => $reservaActual['carro'],
                'fecha' => $data['fecha'],
                'hora_inicio' => $data['hora_inicio'],
                'hora_fin' => $data['hora_fin'],
                'num_portatiles' => $reservaActual['num_portatiles'],
                'excluir_id' => $id
            ]);

            if (!$disponibilidad['disponible']) {
                throw new ValidationException([
                    "No hay suficientes portátiles disponibles en el nuevo horario. " .
                    "Disponibles: {$disponibilidad['unidades_disponibles']}"
                ]);
            }

            $result = $this->model->updateFechas($id, $data['fecha'], $data['hora_inicio'], $data['hora_fin']);

            if (!$result) {
                return [
                    'status' => 'no_changes',
                    'message' => 'No hubo cambios en las fechas'
                ];
            }

            return [
                'status' => 'updated',
                'message' => 'Fechas actualizadas correctamente'
            ];

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al actualizar fechas: " . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar solo unidades
     */
    public function updateUnidades(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'num_portatiles' => 'required|int|min:1'
        ]);

        try {
            // Obtener reserva actual
            $reservaActual = $this->getReservaById($id);

            // Verificar disponibilidad para las nuevas unidades
            $disponibilidad = $this->checkDisponibilidad([
                'carro' => $reservaActual['carro'],
                'fecha' => date('Y-m-d', strtotime($reservaActual['inicio'])),
                'hora_inicio' => date('H:i', strtotime($reservaActual['inicio'])),
                'hora_fin' => date('H:i', strtotime($reservaActual['fin'])),
                'num_portatiles' => $data['num_portatiles'],
                'excluir_id' => $id
            ]);

            if (!$disponibilidad['disponible']) {
                throw new ValidationException([
                    "No hay suficientes portátiles disponibles. " .
                    "Disponibles: {$disponibilidad['unidades_disponibles']}"
                ]);
            }

            $result = $this->model->updateUnidades($id, $data['num_portatiles']);

            if (!$result) {
                return [
                    'status' => 'no_changes',
                    'message' => 'No hubo cambios en las unidades'
                ];
            }

            return [
                'status' => 'updated',
                'message' => 'Unidades actualizadas correctamente'
            ];

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al actualizar unidades: " . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar reserva
     */
    public function deleteReserva(int $id): void
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            // Verificar que la reserva existe
            $this->getReservaById($id);

            $result = $this->model->delete($id);
            
            if (!$result) {
                throw new \Exception("No se pudo eliminar la reserva", 500);
            }

        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al eliminar la reserva: " . $e->getMessage(), 500);
        }
    }
}