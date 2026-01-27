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
            // Solicita al modelo todos los registros de Espacios
            return $this->model->getAll();
        } catch (Throwable $e) {
            // Captura cualquier error de base de datos y lo transforma en un error interno controlado
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    public function getEspacioById(string $id): array
    {
        Validator::validate(['id' => $id], [
            'id' => 'required|string|min:1'
        ]);

        try {
            $caracteristica = $this->model->findById($id);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }

        if (!$caracteristica) {
            throw new \Exception("Espacio no encontrado", 404);
        }

        return $caracteristica;
    }
}