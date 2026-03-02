<?php
declare(strict_types=1);

namespace Services;

use Models\EdificioModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class EdificioService
{
    private EdificioModel $model;

    public function __construct()
    {
        $this->model = new EdificioModel();
    }

    /**
     * Obtener todos los edificios
     */
    public function getAllEdificios(): array
    {
        try {
            return $this->model->getAll();
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener edificios: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener edificio por ID
     */
    public function getEdificioById(int $id): array
    {
        if ($id <= 0) {
            throw new ValidationException(["ID de edificio no válido"]);
        }

        try {
            $edificio = $this->model->findById($id);
            
            if (!$edificio) {
                throw new ValidationException(["Edificio no encontrado"]);
            }
            
            return $edificio;
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener edificio: " . $e->getMessage(), 500);
        }
    }

    /**
     * Crear nuevo edificio
     */
    public function createEdificio(array $data): array
    {
        // Validar datos
        if (!isset($data['nombre_edificio']) || empty(trim($data['nombre_edificio']))) {
            throw new ValidationException(["El nombre del edificio es obligatorio"]);
        }

        // Capitalizar nombre
        $data['nombre_edificio'] = ucfirst(strtolower(trim($data['nombre_edificio'])));

        try {
            $edificio = $this->model->create($data);
            
            if (!$edificio || !isset($edificio['id_edificio'])) {
                throw new \Exception("No se pudo crear el edificio");
            }

            return $edificio;

        } catch (Throwable $e) {
            // Verificar si es error de duplicado
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                throw new ValidationException(["Ya existe un edificio con ese nombre"]);
            }
            throw new \Exception("Error al crear edificio: " . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar edificio
     */
    public function updateEdificio(int $id, array $data): array
    {
        if ($id <= 0) {
            throw new ValidationException(["ID de edificio no válido"]);
        }

        // Validar datos
        if (!isset($data['nombre_edificio']) || empty(trim($data['nombre_edificio']))) {
            throw new ValidationException(["El nombre del edificio es obligatorio"]);
        }

        // Capitalizar nombre
        $data['nombre_edificio'] = ucfirst(strtolower(trim($data['nombre_edificio'])));

        try {
            // Verificar que existe
            $this->getEdificioById($id);

            $edificio = $this->model->update($id, $data);
            
            if (!$edificio) {
                throw new \Exception("No se pudo actualizar el edificio");
            }

            return $edificio;

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            // Verificar si es error de duplicado
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                throw new ValidationException(["Ya existe otro edificio con ese nombre"]);
            }
            throw new \Exception("Error al actualizar edificio: " . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar edificio
     */
    public function deleteEdificio(int $id): void
    {
        if ($id <= 0) {
            throw new ValidationException(["ID de edificio no válido"]);
        }

        try {
            // Verificar que existe
            $this->getEdificioById($id);

            $this->model->delete($id);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            // Verificar si es error de FK (tiene plantas asociadas)
            if (strpos($e->getMessage(), 'foreign key') !== false) {
                throw new ValidationException(["No se puede eliminar porque tiene plantas asociadas"]);
            }
            throw new \Exception("Error al eliminar edificio: " . $e->getMessage(), 500);
        }
    }
}