<?php
declare(strict_types=1);

namespace Services;

use Models\PortatilModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class PortatilService
{
    private PortatilModel $model;

    public function __construct()
    {
        $this->model = new PortatilModel();
    }

    /**
     * ===========================================
     * MATERIALES (CARROS DE PORTÁTILES)
     * ===========================================
     */

    /**
     * GET /portatiles/materiales
     * Obtener todos los materiales
     */
    public function getAllMateriales(): array
    {
        try {
            return $this->model->getAllMateriales();
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener materiales: " . $e->getMessage(), 500);
        }
    }

    /**
     * GET /portatiles/materiales/{id}
     * Obtener material por ID
     */
    public function getMaterialById(string $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        try {
            $material = $this->model->findMaterialById($id);
            
            if (!$material) {
                throw new \Exception("Material no encontrado", 404);
            }
            
            return $material;
            
        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * POST /portatiles/materiales
     * Crear nuevo material
     */
    public function createMaterial(array $input): array
    {
        $data = Validator::validate($input, [
            'id_recurso' => 'required|string|min:1|max:10',
            'descripcion' => 'required|string|min:3|max:255',
            'id_edificio' => 'required|int|min:1',
            'numero_planta' => 'required|int|min:-10|max:100',
            'unidades' => 'required|int|min:1|max:1000',
            'activo' => 'boolean',
            'especial' => 'boolean'
        ]);

        // Asegurar valores por defecto para campos opcionales
        $data['activo'] = isset($data['activo']) ? (int)$data['activo'] : 1;
        $data['especial'] = isset($data['especial']) ? (int)$data['especial'] : 0;

        try {
            // Verificar si ya existe
            if ($this->model->materialExists($data['id_recurso'])) {
                throw new ValidationException(["El ID del material ya existe"]);
            }

            $result = $this->model->createMaterial($data);
            
            if (!$result) {
                throw new \Exception("No se pudo crear el material");
            }

            return [
                'id' => $data['id_recurso'],
                'message' => 'Material creado correctamente'
            ];

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error al crear material: " . $e->getMessage(), 500);
        }
    }

    /**
     * PUT /portatiles/materiales/{id}
     * Actualizar material
     */
    public function updateMaterial(string $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        $data = Validator::validate($input, [
            'descripcion' => 'required|string|min:3|max:255',
            'unidades' => 'required|int|min:1|max:1000',
            'activo' => 'required|boolean',
            'especial' => 'boolean'
        ]);

        // Asegurar que especial sea un entero (0 o 1)
        $data['especial'] = isset($data['especial']) ? (int)$data['especial'] : 0;
        $data['activo'] = (int)$data['activo'];

        try {
            // Verificar que existe
            $materialExistente = $this->getMaterialById($id);

            $result = $this->model->updateMaterial($id, $data);

            if (!$result) {
                return [
                    'status' => 'no_changes',
                    'message' => 'No hubo cambios en el material'
                ];
            }

            return [
                'status' => 'updated',
                'message' => 'Material actualizado correctamente'
            ];

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al actualizar material: " . $e->getMessage(), 500);
        }
    }

    /**
     * DELETE /portatiles/materiales/{id}
     * Eliminar material
     */
    public function deleteMaterial(string $id): void
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        try {
            // Verificar que existe
            $this->getMaterialById($id);

            $result = $this->model->deleteMaterial($id);
            
            if (!$result) {
                throw new \Exception("No se pudo eliminar el material", 500);
            }

        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al eliminar material: " . $e->getMessage(), 500);
        }
    }

    /**
     * ===========================================
     * RESERVAS DE PORTÁTILES
     * ===========================================
     */

    /**
     * GET /portatiles/reservas
     * Obtener todas las reservas de portátiles
     */
    public function getAllReservas(): array
    {
        try {
            return $this->model->getAllReservas();
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener reservas: " . $e->getMessage(), 500);
        }
    }

    /**
     * GET /portatiles/reservas/usuario/{id_usuario}
     * Obtener reservas de portátiles por usuario
     */
    public function getReservasByUsuario(int $idUsuario): array
    {
        Validator::validate(['id_usuario' => $idUsuario], [
            'id_usuario' => 'required|int|min:1'
        ]);

        try {
            return $this->model->getReservasByUsuario($idUsuario);
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener reservas del usuario: " . $e->getMessage(), 500);
        }
    }

    /**
     * GET /portatiles/reservas/{id}
     * Obtener reserva de portátil por ID de reserva
     */
    public function getReservaById(int $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try {
            $reserva = $this->model->findReservaById($id);
            
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
     * POST /portatiles/reservas
     * Crear nueva reserva de portátil
     */
    public function createReserva(array $input): array
    {
        // Primero, eliminar autorizada de la validación si no existe
        $reglas = [
            'id_material' => 'required|string|min:1|max:10',
            'id_usuario' => 'required|int|min:1',
            'usaenespacio' => 'required|string|min:1|max:10',
            'unidades' => 'required|int|min:1',
            'inicio' => 'required|date',
            'fin' => 'required|date',
            'asignatura' => 'required|string|min:1|max:100',
            'grupo' => 'required|string|min:1|max:50',
            'profesor' => 'required|string|min:1|max:100',
            'observaciones' => 'string|max:500'
        ];

        // Solo validar autorizada si viene en el input
        if (isset($input['autorizada'])) {
            $reglas['autorizada'] = 'boolean';
        }

        $data = Validator::validate($input, $reglas);

        // Validar que fin sea mayor que inicio
        if (strtotime($data['fin']) <= strtotime($data['inicio'])) {
            throw new ValidationException(["La fecha de fin debe ser posterior a la fecha de inicio"]);
        }

        // Asignar valor por defecto a autorizada si no viene
        $data['autorizada'] = $data['autorizada'] ?? 0;

        try {
            // Verificar que el material existe
            $material = $this->getMaterialById($data['id_material']);

            // Verificar disponibilidad
            $unidadesTotales = $this->model->getMaterialUnidades($data['id_material']);
            
            $unidadesReservadas = $this->model->checkDisponibilidad(
                $data['id_material'],
                $data['inicio'],
                $data['fin'],
                $data['unidades']
            );

            $unidadesDisponibles = $unidadesTotales - $unidadesReservadas;
            
            if ($unidadesDisponibles < $data['unidades']) {
                throw new ValidationException([
                    "No hay suficientes unidades disponibles. " .
                    "Disponibles: $unidadesDisponibles, Solicitadas: {$data['unidades']}"
                ]);
            }

            $id = $this->model->createReserva($data);
            
            if (!$id) {
                throw new \Exception("No se pudo crear la reserva");
            }

            return [
                'id' => $id,
                'message' => 'Reserva creada correctamente'
            ];

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error al crear reserva: " . $e->getMessage(), 500);
        }
    }

    /**
     * POST /portatiles/reservas/disponibilidad
     * Verificar disponibilidad de un portátil en un rango de fechas
     */
    public function checkDisponibilidad(array $input): array
    {
        $data = Validator::validate($input, [
            'id_material' => 'required|string|min:1|max:10',
            'inicio' => 'required|date',
            'fin' => 'required|date',
            'unidades' => 'required|int|min:1',
            'excluir_id' => 'int|min:1'
        ]);

        // Validar que fin sea mayor que inicio
        if (strtotime($data['fin']) <= strtotime($data['inicio'])) {
            throw new ValidationException(["La fecha de fin debe ser posterior a la fecha de inicio"]);
        }

        try {
            // Verificar que el material existe
            $this->getMaterialById($data['id_material']);

            // Obtener unidades totales del material
            $unidadesTotales = $this->model->getMaterialUnidades($data['id_material']);
            
            // Verificar disponibilidad
            $unidadesReservadas = $this->model->checkDisponibilidad(
                $data['id_material'],
                $data['inicio'],
                $data['fin'],
                $data['unidades'],
                $data['excluir_id'] ?? null
            );

            $unidadesDisponibles = $unidadesTotales - $unidadesReservadas;
            $disponible = $unidadesDisponibles >= $data['unidades'];

            return [
                'disponible' => $disponible,
                'unidades_totales' => $unidadesTotales,
                'unidades_reservadas' => $unidadesReservadas,
                'unidades_disponibles' => $unidadesDisponibles,
                'unidades_solicitadas' => $data['unidades']
            ];

        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al verificar disponibilidad: " . $e->getMessage(), 500);
        }
    }

    /**
    * PUT /portatiles/reservas/{id}
    * Actualizar reserva completa
    */
    public function updateReserva(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        // Construir reglas de validación dinámicamente
        $reglas = [
            'id_material' => 'required|string|min:1|max:10',
            'usaenespacio' => 'required|string|min:1|max:10',
            'unidades' => 'required|int|min:1',
            'inicio' => 'required|date',
            'fin' => 'required|date',
            'asignatura' => 'required|string|min:1|max:100',
            'grupo' => 'required|string|min:1|max:50',
            'profesor' => 'required|string|min:1|max:100',
            'observaciones' => 'string|max:500'
        ];

        // Solo validar autorizada si viene en el input
        if (isset($input['autorizada'])) {
            $reglas['autorizada'] = 'boolean';
        }

        $data = Validator::validate($input, $reglas);

        // Validar que fin sea mayor que inicio
        if (strtotime($data['fin']) <= strtotime($data['inicio'])) {
            throw new ValidationException(["La fecha de fin debe ser posterior a la fecha de inicio"]);
        }

        // Asignar valor por defecto si no viene
        $data['autorizada'] = $data['autorizada'] ?? 0;

        try {
            // Verificar que la reserva existe
            $reservaExistente = $this->getReservaById($id);

            // Verificar que el material existe
            $this->getMaterialById($data['id_material']);

            // Verificar disponibilidad excluyendo esta reserva
            $unidadesTotales = $this->model->getMaterialUnidades($data['id_material']);
            
            $unidadesReservadas = $this->model->checkDisponibilidad(
                $data['id_material'],
                $data['inicio'],
                $data['fin'],
                $data['unidades'],
                $id
            );

            $unidadesDisponibles = $unidadesTotales - $unidadesReservadas;
            
            if ($unidadesDisponibles < $data['unidades']) {
                throw new ValidationException([
                    "No hay suficientes unidades disponibles. " .
                    "Disponibles: $unidadesDisponibles, Solicitadas: {$data['unidades']}"
                ]);
            }

            $result = $this->model->updateReserva($id, $data);

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
            throw new \Exception("Error al actualizar reserva: " . $e->getMessage(), 500);
        }
    }

    /**
     * PATCH /portatiles/reservas/{id}
     * Actualizar parcialmente una reserva (solo fechas)
     */
    public function patchFechas(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'inicio' => 'required|date',
            'fin' => 'required|date'
        ]);

        // Validar que fin sea mayor que inicio
        if (strtotime($data['fin']) <= strtotime($data['inicio'])) {
            throw new ValidationException(["La fecha de fin debe ser posterior a la fecha de inicio"]);
        }

        try {
            // Obtener reserva actual
            $reservaActual = $this->getReservaById($id);

            // Verificar disponibilidad para las nuevas fechas
            $unidadesTotales = $this->model->getMaterialUnidades($reservaActual['id_material']);
            
            $unidadesReservadas = $this->model->checkDisponibilidad(
                $reservaActual['id_material'],
                $data['inicio'],
                $data['fin'],
                $reservaActual['unidades'],
                $id
            );

            $unidadesDisponibles = $unidadesTotales - $unidadesReservadas;
            
            if ($unidadesDisponibles < $reservaActual['unidades']) {
                throw new ValidationException([
                    "No hay suficientes unidades disponibles en el nuevo horario. " .
                    "Disponibles: $unidadesDisponibles"
                ]);
            }

            $result = $this->model->patchFechas($id, $data['inicio'], $data['fin']);

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
     * PATCH /portatiles/reservas/{id}/unidades
     * Actualizar solo el número de unidades de una reserva
     */
    public function patchUnidades(int $id, array $input): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'unidades' => 'required|int|min:1'
        ]);

        try {
            // Obtener reserva actual
            $reservaActual = $this->getReservaById($id);

            // Verificar disponibilidad para las nuevas unidades
            $unidadesTotales = $this->model->getMaterialUnidades($reservaActual['id_material']);
            
            $unidadesReservadas = $this->model->checkDisponibilidad(
                $reservaActual['id_material'],
                $reservaActual['inicio'],
                $reservaActual['fin'],
                $data['unidades'],
                $id
            );

            $unidadesDisponibles = $unidadesTotales - $unidadesReservadas;
            
            if ($unidadesDisponibles < $data['unidades']) {
                throw new ValidationException([
                    "No hay suficientes unidades disponibles. " .
                    "Disponibles: $unidadesDisponibles"
                ]);
            }

            $result = $this->model->patchUnidades($id, $data['unidades']);

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
     * DELETE /portatiles/reservas/{id}
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

            $result = $this->model->deleteReserva($id);
            
            if (!$result) {
                throw new \Exception("No se pudo eliminar la reserva", 500);
            }

        } catch (Throwable $e) {
            if ($e->getCode() === 404) throw $e;
            throw new \Exception("Error al eliminar reserva: " . $e->getMessage(), 500);
        }
    }
}