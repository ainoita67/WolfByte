<?php
declare(strict_types=1);

namespace Services;

use Models\EspacioModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class EspacioService
{
    private EspacioModel $model;

    public function __construct()
    {
        $this->model = new EspacioModel();
    }

    /**
     * Obtener todos los espacios
     */
    public function getAllEspacios(): array
    {
        try {
            return $this->model->getAll();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener espacio por ID
     */
    public function getEspacioById(string $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        try {
            $espacio = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$espacio) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        return $espacio;
    }

    // Crear espacio
    public function createEspacio(array $input): array
    {
        try {
            $data = Validator::validate($input, [
                'id_espacio' => 'required|string|min:1|max:10',
                'descripcion' => 'string|max:255',
                'es_aula' => 'required|int',
                'activo' => 'required|int',
                'especial' => 'required|int',
                'numero_planta' => 'int|min:0|max:5',
                'id_edificio' => 'int|min:1',
                'nombre_edificio' => 'string|min:1|max:100',
                'caracteristicasId' => ''
            ]);

        } catch (ValidationException $e) {
            throw new \Exception("Error de validación: " . json_encode($e->errors), 400);
        }

        if (empty($data['id_edificio']) && !empty($data['nombre_edificio'])) {

            $data['id_edificio'] = null;
        }

        if (empty($data['id_edificio'])) {
            throw new \Exception("Debe proporcionar id_edificio", 400);
        }

        try {
            $id = $this->model->create($data);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        return ['id' => $id];
    }

    /**
     * Actualizar espacio
     */
    public function updateEspacio(string $id, array $input): array
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1|max:10'
        ]);

        // Validar datos de entrada
        $data = Validator::validate($input, [
            'descripcion' => 'string|max:255',
            'es_aula' => 'required|int',
            'activo' => 'required|int',
            'especial' => 'required|int',
            'numero_planta' => 'int|min:0|max:5',
            'id_edificio' => 'int|min:1',
            'nombre_edificio' => 'string|min:1|max:100',
            'caracteristicasId' => ''
        ]);

        // Verificar que el espacio exista antes de actualizar
        try {
            $exists = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$exists) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        try {
            // var_dump($data);
            $result = $this->model->update($id, $data);

            if ($result === 0) {
                // Puede ser que no haya cambios o que no se encontró el registro
                return [
                    'status' => 'no_changes',
                    'message' => 'No hubo cambios en los datos del espacio'
                ];
            }

            return [
                'status' => 'updated',
                'message' => 'Espacio actualizado correctamente',
                'affected_rows' => $result
            ];

        } catch (Throwable $e) {
            throw new \Exception("Error al actualizar espacio: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener espacios por edificio
     */
    public function getEspaciosByEdificio(int $idEdificio): array
    {
        Validator::validate(['id_edificio' => $idEdificio], [
            'id_edificio' => 'required|int|min:1'
        ]);

        try {
            return $this->model->getByEdificio($idEdificio);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener espacios activos
     */
    public function getEspaciosActivos(): array
    {
        try {
            return $this->model->getActivos();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Cambiar estado activo/inactivo de un espacio
     */
    public function toggleEstadoEspacio(string $id, int $estado): array
    {
        Validator::validate([
            'id' => $id,
            'estado' => $estado
        ], [
            'id' => 'required|string|min:1|max:10',
            'estado' => 'required|int|in:0,1'
        ]);

        try {
            // Verificar que el espacio exista
            $exists = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$exists) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        try {
            $success = $this->model->toggleEstado($id, $estado);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$success) {
            throw new \Exception("No se pudo cambiar el estado del espacio", 500);
        }

        $estadoTexto = $estado === 1 ? 'activado' : 'desactivado';
        return [
            'status' => 'updated',
            'message' => "Espacio {$estadoTexto} correctamente"
        ];
    }

    public function getAllAulas(): array
    {
        try {
            $aulas = $this->model->getAllAulas();
            $resultado = [];

            foreach ($aulas as $aula) {

                $edificio = $aula['nombre_edificio'] ?: 'Sin edificio';
                $planta = $aula['nombre_planta'] ?: 'Sin planta';

                if (!isset($resultado[$edificio])) {
                    $resultado[$edificio] = [];
                }

                if (!isset($resultado[$edificio][$planta])) {
                    $resultado[$edificio][$planta] = [];
                }

                // Obtener características
                $caracteristicas = $this->model->getCaracteristicasEspacio($aula['id_recurso']) ?? [];

                $aulaFormateada = [
                    'id_recurso' => $aula['id_recurso'],
                    'descripcion' => $aula['descripcion'],
                    'caracteristicas' => $caracteristicas
                ];

                $resultado[$edificio][$planta][] = $aulaFormateada;
            }

            return $resultado;

        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    public function getAulasDisponibles(array $input): array
    {
        $data = Validator::validate($input, [
            'fecha'     => 'required|string',
            'hora_inicio'    => 'required|string',
            'hora_fin'       => 'required|string',

        ]);

        $fecha = $data['fecha'];
        $hora_inicio = $data['hora_inicio'];
        $hora_fin = $data['hora_fin'];
        $dia_semana = date('w', strtotime($fecha));
        $inicio = date('Y-m-d', strtotime($fecha)) . ' ' . $hora_inicio;
        $fin = date('Y-m-d', strtotime($fecha)) . ' ' . $hora_fin;

        try {

            $aulas = $this->model->getAulasLibres($inicio, $fin, $dia_semana, $hora_inicio, $hora_fin);
            $resultado = [];

            foreach ($aulas as $aula) {

                $edificio = $aula['nombre_edificio'] ?: 'Sin edificio';
                $planta = $aula['nombre_planta'] ?: 'Sin planta';

                if (!isset($resultado[$edificio])) {
                    $resultado[$edificio] = [];
                }

                if (!isset($resultado[$edificio][$planta])) {
                    $resultado[$edificio][$planta] = [];
                }

                // Obtener características
                $caracteristicas = $this->model->getCaracteristicasEspacio($aula['id_recurso']) ?? [];

                $aulaFormateada = [
                    'id_recurso' => $aula['id_recurso'],
                    'descripcion' => $aula['descripcion'],
                    'reservas' => $aula['total_reservas'],
                    'caracteristicas' => $caracteristicas
                ];

                $resultado[$edificio][$planta][] = $aulaFormateada;
            }

            return $resultado;

    /**
     * Obtener espacios libres entre dos fechas
     */
    public function getEspaciosLibresEntreFechas(array $input): array
    {
        // Validar fechas
        $data = Validator::validate($input, [
            'fecha_inicio' => 'required|string|min:10|max:19',
            'fecha_fin' => 'required|string|min:10|max:19'
        ]);

        // Validar formato de fechas
        $fechaInicio = strtotime($data['fecha_inicio']);
        $fechaFin = strtotime($data['fecha_fin']);

        if (!$fechaInicio || !$fechaFin) {
            throw new \Exception("Formato de fecha inválido. Use YYYY-MM-DD HH:MM:SS", 400);
        }

        if ($fechaInicio >= $fechaFin) {
            throw new \Exception("La fecha de inicio debe ser anterior a la fecha de fin", 400);
        }

        try {
            $espacios = $this->model->getEspaciosLibres($data['fecha_inicio'], $data['fecha_fin']);

            return [
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'total_espacios' => count($espacios),
                'espacios' => $espacios
            ];
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

}