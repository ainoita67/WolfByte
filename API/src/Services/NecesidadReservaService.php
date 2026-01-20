<?php
declare(strict_types=1);

namespace Services;

use Validation\Validator;
use Validation\ValidationException;
use Models\NecesidadReservaModel;
use PDOException;

class NecesidadReservaService
{
    private NecesidadReservaModel $model;

    public function __construct()
    {
        $this->model = new NecesidadReservaModel();
    }

    /**
     * Crear una nueva necesidad de reserva
     */
    public function create(array $data): array
    {
        // Validar datos requeridos para la reserva
        Validator::validate($data, [
            'usuario_id'      => 'required|integer',
            'fecha'           => 'required|date',
            'hora'            => 'required|string', // Formato HH:MM
            'cantidad_personas'=> 'required|integer|min:1',
            'tipo_servicio'   => 'required|string|max:100'
        ]);

        try {
            $id = $this->model->create([
                'usuario_id'      => $data['usuario_id'],
                'fecha'           => $data['fecha'],
                'hora'            => $data['hora'],
                'cantidad_personas'=> $data['cantidad_personas'],
                'tipo_servicio'   => $data['tipo_servicio'],
                'notas'           => $data['notas'] ?? null
            ]);
        } catch (PDOException $e) {
            throw new \Exception("No se pudo crear la necesidad de reserva", 500);
        }

        return $this->model->findById((int)$id);
    }

    /**
     * Obtener todas las necesidades de un usuario
     */
    public function getByUsuario(int $usuarioId): array
    {
        try {
            return $this->model->findByUsuario($usuarioId);
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener necesidades de reserva", 500);
        }
    }

    /**
     * Cancelar o eliminar una necesidad de reserva
     */
    public function delete(int $usuarioId, int $id): bool
    {
        try {
            return $this->model->deleteByUsuario($usuarioId, $id);
        } catch (PDOException $e) {
            throw new \Exception("No se pudo eliminar la necesidad de reserva", 500);
        }
    }

    public function update(int $id, array $data): array
    {
        Validator::validate($data, [
            'fecha'           => 'required|date',       
            'hora'            => 'required|string',
            'cantidad_personas'=> 'required|integer|min:1',
            'tipo_servicio'   => 'required|string|max:100',
        ]);

        try {
            $updated = $this->model->update($id, $data);
            if (!$updated) {
                throw new \Exception("No se pudo actualizar la necesidad de reserva", 400);
            }
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la necesidad de reserva", 500);
        }

        return $this->model->findById($id);
    }

}
