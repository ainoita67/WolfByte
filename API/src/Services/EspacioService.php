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
}