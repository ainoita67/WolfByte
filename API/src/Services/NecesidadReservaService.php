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
     * Asignar una necesidad a una reserva-espacio
     */
    public function create(int $idReservaEspacio, int $idNecesidad): array
    {
        try {
            $this->model->create([
                'id_reserva_espacio' => $idReservaEspacio,
                'id_necesidad'       => $idNecesidad
            ]);
        } catch (PDOException $e) {
            throw new \Exception("No se pudo asignar la necesidad", 500);
        }

        return $this->model->findOne($idReservaEspacio, $idNecesidad);
    }

    /**
     * Obtener necesidades de una reserva
     */
    public function getByReserva(int $idReserva): array
    {
        try {
            return $this->model->findByReserva($idReserva);
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener necesidades de la reserva", 500);
        }
    }

    /**
     * Obtener una relación específica
     */
    public function getOne(int $idReservaEspacio, int $idNecesidad): ?array
    {
        try {
            return $this->model->findOne($idReservaEspacio, $idNecesidad);
        } catch (PDOException $e) {
            throw new \Exception("Error al obtener la relación", 500);
        }
    }

    /**
     * Cambiar una necesidad por otra
     */
    public function update(int $idReservaEspacio, int $idNecesidadActual, int $idNecesidadNueva): array
    {
        try {
            $ok = $this->model->update(
                $idReservaEspacio,
                $idNecesidadActual,
                $idNecesidadNueva
            );

            if (!$ok) {
                throw new \Exception("No se pudo actualizar la necesidad", 400);
            }
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar la necesidad", 500);
        }

        return $this->model->findOne($idReservaEspacio, $idNecesidadNueva);
    }

    /**
     * Quitar una necesidad de una reserva
     */
    public function delete(int $idReservaEspacio, int $idNecesidad): bool
    {
        try {
            return $this->model->delete($idReservaEspacio, $idNecesidad);
        } catch (PDOException $e) {
            throw new \Exception("No se pudo eliminar la necesidad", 500);
        }
    }

    /**
     * Reemplazar todas las necesidades de una reserva
     */
    public function sync(int $idReservaEspacio, array $necesidades): void
    {
        try {
            $this->model->deleteAllByReservaEspacio($idReservaEspacio);

            foreach ($necesidades as $idNecesidad) {
                $this->model->create([
                    'id_reserva_espacio' => $idReservaEspacio,
                    'id_necesidad'       => (int)$idNecesidad
                ]);
            }
        } catch (PDOException $e) {
            throw new \Exception("No se pudieron sincronizar las necesidades", 500);
        }
    }
}
