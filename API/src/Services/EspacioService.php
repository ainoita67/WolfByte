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
        // Validar datos requeridos
        $data = Validator::validate($input, [
            'id_recurso' => 'required|string|min:3|max:10',
            'descripcion' => 'required|string|min:5|max:255',
            'numero_planta' => 'required|int|min:0|max:20',
            'id_edificio' => 'required|int|min:1',
            'es_aula' => 'required|boolean',
            'activo' => 'boolean',
            'especial' => 'boolean',
            'caracteristicas' => 'array'
        ]);

        // Validaciones adicionales específicas para espacio
        if (!preg_match('/^[A-Z0-9\-]+$/i', $data['id_recurso'])) {
            throw new \Exception("El ID del recurso solo puede contener letras, números y guiones", 400);
        }

        // Validar que caracteristicas contenga solo números si está presente
        if (isset($data['caracteristicas']) && is_array($data['caracteristicas'])) {
            foreach ($data['caracteristicas'] as $caracteristica) {
                if (!is_numeric($caracteristica)) {
                    throw new \Exception("Las características deben ser IDs numéricos", 400);
                }
            }
            // Convertir a enteros
            $data['caracteristicas'] = array_map('intval', $data['caracteristicas']);
        }

        try {
            $result = $this->model->create($data);
        } catch (Throwable $e) {
            // Capturar errores específicos de la base de datos
            $errorMessage = $e->getMessage();

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
            throw new \Exception("No se pudo crear el espacio", 500);
        }

        // Obtener el espacio creado para devolverlo completo
        return $this->getEspacioById($data['id_recurso']);
    }

    public function updateEspacio(string $id, array $input): array
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:3|max:10'
        ]);

        // Validar datos a actualizar
        $data = Validator::validate($input, [
            'descripcion' => 'string|min:5|max:255',
            'numero_planta' => 'int|min:0|max:20',
            'id_edificio' => 'int|min:1',
            'es_aula' => 'boolean',
            'activo' => 'boolean',
            'especial' => 'boolean',
            'caracteristicas' => 'array'
        ]);

        // Verificar que al menos un campo sea proporcionado
        if (empty($data)) {
            throw new \Exception("No se proporcionaron datos para actualizar", 400);
        }

        // Validar que caracteristicas contenga solo números si está presente
        if (isset($data['caracteristicas'])) {
            if (!is_array($data['caracteristicas'])) {
                throw new \Exception("Las características deben ser un array", 400);
            }

            foreach ($data['caracteristicas'] as $caracteristica) {
                if (!is_numeric($caracteristica)) {
                    throw new \Exception("Las características deben ser IDs numéricos", 400);
                }
            }
            // Convertir a enteros
            $data['caracteristicas'] = array_map('intval', $data['caracteristicas']);
        }

        // Verificar que el espacio existe antes de actualizar
        $espacioExistente = $this->model->findById($id);
        if (!$espacioExistente) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        // Si se va a cambiar el edificio o planta, verificar que el nuevo edificio existe
        if (isset($data['id_edificio']) || isset($data['numero_planta'])) {
            // Aquí podrías agregar validación adicional si es necesario
        }

        try {
            $result = $this->model->update($id, $data);
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'foreign key constraint fails')) {
                if (str_contains($errorMessage, 'Edificio')) {
                    throw new \Exception("El edificio especificado no existe", 404);
                }
                throw new \Exception("Error de integridad referencial", 400);
            }

            throw new \Exception("Error interno en la base de datos: " . $errorMessage, 500);
        }

        if ($result === 0) {
            // Verificar si realmente no hubo cambios
            $espacioActualizado = $this->model->findById($id);

            // Comparar si hubo cambios reales
            $huboCambios = false;
            foreach ($data as $key => $value) {
                if ($key === 'caracteristicas')
                    continue; // No comparar características directamente

                if (isset($espacioExistente[$key]) && isset($espacioActualizado[$key])) {
                    if ($espacioExistente[$key] != $value) {
                        $huboCambios = true;
                        break;
                    }
                }
            }

            if (!$huboCambios) {
                return [
                    'status' => 'no_changes',
                    'message' => 'No hubo cambios en los datos del espacio'
                ];
            }

            throw new \Exception("No se pudo actualizar el espacio", 500);
        }

        if ($result === false) {
            throw new \Exception("Error al actualizar el espacio", 500);
        }

        // Obtener el espacio actualizado
        $espacioActualizado = $this->getEspacioById($id);

        return [
            'status' => 'updated',
            'message' => 'Espacio actualizado correctamente',
            'data' => $espacioActualizado
        ];
    }

    public function deleteEspacio(string $id): void
    {
        // Validar ID
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:3|max:10'
        ]);

        try {
            // Verificar que el espacio existe
            $espacio = $this->model->findById($id);
            if (!$espacio) {
                throw new \Exception("Espacio no encontrado", 404);
            }

            // Verificar si el espacio tiene reservas activas
            $tieneReservas = $this->tieneReservasActivas($id);
            if ($tieneReservas) {
                throw new \Exception("No se puede eliminar el espacio porque tiene reservas activas", 409);
            }

            // Ejecutar el delete en el modelo
            $result = $this->model->delete($id);
        } catch (Throwable $e) {
            // Capturar errores específicos
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'foreign key constraint fails')) {
                throw new \Exception("No se puede eliminar el espacio: está siendo utilizado en otras tablas", 409);
            }

            // Si ya es una excepción con código, relanzarla
            if ($e instanceof \Exception && $e->getCode() !== 0) {
                throw $e;
            }

            throw new \Exception("Error interno en la base de datos: " . $errorMessage, 500);
        }

        // Devolver resultados
        if ($result === 0) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        if ($result === false) {
            throw new \Exception("No se pudo eliminar el espacio", 500);
        }

        // Eliminación exitosa → no retorna nada
    }

    public function getEspaciosByEdificio(int $idEdificio): array
    {
        Validator::validate(['id_edificio' => $idEdificio], [
            'id_edificio' => 'required|int|min:1'
        ]);

        try {
            return $this->model->findByEdificio($idEdificio);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    public function verificarDisponibilidad(string $idEspacio, string $inicio, string $fin): array
    {
        Validator::validate([
            'id_espacio' => $idEspacio,
            'inicio' => $inicio,
            'fin' => $fin
        ], [
            'id_espacio' => 'required|string|min:3|max:10',
            'inicio' => 'required|string|min:10',
            'fin' => 'required|string|min:10'
        ]);

        // Validar formato de fechas
        if (!strtotime($inicio) || !strtotime($fin)) {
            throw new \Exception("Formato de fecha inválido", 400);
        }

        // Validar que inicio sea anterior a fin
        if (strtotime($inicio) >= strtotime($fin)) {
            throw new \Exception("La fecha de inicio debe ser anterior a la fecha de fin", 400);
        }

        // Verificar que el espacio existe
        $espacio = $this->model->findById($idEspacio);
        if (!$espacio) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        try {
            $disponible = $this->model->estaDisponible($idEspacio, $inicio, $fin);
        } catch (Throwable $e) {
            throw new \Exception("Error al verificar disponibilidad: " . $e->getMessage(), 500);
        }

        return [
            'disponible' => $disponible,
            'espacio' => $espacio,
            'intervalo' => [
                'inicio' => $inicio,
                'fin' => $fin
            ]
        ];
    }

    private function tieneReservasActivas(string $idEspacio): bool
    {
        try {
            // Verificar reservas futuras o actuales
            $hoy = date('Y-m-d H:i:s');

            // Esta sería una consulta más específica en el modelo
            // Por ahora usaremos una aproximación
            return false; // Cambiar esto cuando implementes la lógica real
        } catch (Throwable $e) {
            // Si hay error, asumimos que no tiene reservas para no bloquear la eliminación
            error_log("Error al verificar reservas: " . $e->getMessage());
            return false;
        }
    }
    public function getCaracteristicasEspacio(string $idEspacio): array
    {
        // Obtener el espacio primero
        $espacio = $this->getEspacioById($idEspacio);

        // En una implementación real, obtendrías las características desde el modelo
        // Por ahora devolvemos el espacio sin características
        return [
            'espacio' => $espacio,
            'caracteristicas' => []
        ];
    }
}