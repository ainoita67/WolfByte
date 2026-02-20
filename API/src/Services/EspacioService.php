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

    public function getAllEspacios(): array
    {
        try {
            return $this->model->getAll();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

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
    public function createEspacio(array $input): array
    {
        // Validar datos requeridos - CORREGIDO: es_aula NO es required
        $data = Validator::validate($input, [
            'id_recurso' => 'required|string|min:3|max:10',
            'descripcion' => 'required|string|min:5|max:255',
            'numero_planta' => 'required|int|min:0|max:20',
            'id_edificio' => 'required|int|min:1',
            'es_aula' => 'boolean',  // Cambiado de 'required|boolean' a solo 'boolean'
            'activo' => 'boolean',
            'especial' => 'boolean'
        ]);

        // Debug: mostrar datos validados
        error_log("Datos validados: " . print_r($data, true));

        // Asignar valores por defecto si no vienen
        $data['es_aula'] = $data['es_aula'] ?? false;
        $data['activo'] = $data['activo'] ?? true;
        $data['especial'] = $data['especial'] ?? false;

        try {
            $result = $this->model->create($data);

            // Debug: mostrar resultado
            error_log("Resultado de create(): " . ($result ? "true" : "false"));

        } catch (Throwable $e) {
            // Capturar errores específicos de la base de datos
            $errorMessage = $e->getMessage();
            error_log("Error en createEspacio: " . $errorMessage); // Debug

            // Detectar errores comunes
            if (str_contains($errorMessage, 'Duplicate entry')) {
                throw new \Exception("El ID del espacio ya existe", 409);
            }

            if (str_contains($errorMessage, 'foreign key constraint fails')) {
                if (str_contains($errorMessage, 'Edificio')) {
                    throw new \Exception("El edificio especificado no existe", 404);
                }
                throw new \Exception("Error de integridad referencial", 400);
            }

            throw new \Exception("Error interno en la base de datos: " . $errorMessage, 500);
        }

        if (!$result) {
            // Obtener más información del error
            error_log("Resultado falso - Revisar logs de PDO");
            throw new \Exception("No se pudo crear el espacio. Verifique los datos e intente nuevamente.", 500);
        }

        // Obtener el espacio creado para devolverlo completo
        return $this->getEspacioById($data['id_recurso']);
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

        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

}