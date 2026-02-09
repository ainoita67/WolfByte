<?php
declare(strict_types=1);

namespace Services;

use Models\PlantaModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class PlantaService
{
    private PlantaModel $model;

    public function __construct()
    {
        $this->model = new PlantaModel();
    }

    /**
     * Obtener todas las plantas
     */
    public function getAllPlantas(): array
    {
        try {
            return $this->model->getAll();
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener plantas: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener plantas por edificio
     */
    public function getPlantasByEdificio(int $idEdificio): array
    {
        // Validar ID
        Validator::validate(['id_edificio' => $idEdificio], [
            'id_edificio' => 'required|int|min:1'
        ]);

        try {
            $plantas = $this->model->getByEdificio($idEdificio);
            
            if (empty($plantas)) {
                // Verificar si el edificio existe
                $edificioModel = new \Models\EdificioModel();
                $edificio = $edificioModel->findById($idEdificio);
                
                if (!$edificio) {
                    throw new ValidationException(["Edificio no encontrado"]);
                }
                
                return []; // Devolver array vacío si no hay plantas
            }

            return $plantas;
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener plantas del edificio: " . $e->getMessage(), 500);
        }
    }

    /**
     * Crear una nueva planta
     */
    public function createPlanta(int $idEdificio, array $data): array
    {
        // Validar datos
        $validatedData = Validator::validate($data, [
            'numero_planta' => 'required|int|min:-10|max:100' // Permite plantas negativas (subsuelos)
        ]);

        // Validar ID edificio
        Validator::validate(['id_edificio' => $idEdificio], [
            'id_edificio' => 'required|int|min:1'
        ]);

        try {
            // Crear la planta
            $success = $this->model->create($validatedData['numero_planta'], $idEdificio);
            
            if (!$success) {
                throw new \Exception("No se pudo crear la planta", 500);
            }

            // Obtener detalles de la planta creada
            $planta = $this->model->getDetails($validatedData['numero_planta'], $idEdificio);
            
            if (!$planta) {
                throw new \Exception("Planta creada pero no se pudo obtener sus datos", 500);
            }

            return $planta;
        } catch (Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                throw new ValidationException(["La planta ya existe en este edificio"]);
            }
            throw new \Exception("Error al crear planta: " . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar una planta
     * IMPORTANTE: Necesitamos el número de planta actual en los parámetros
     */
    public function updatePlanta(int $numeroPlantaActual, int $idEdificio, array $data): array
    {
        // Validar parámetros
        Validator::validate([
            'numero_planta_actual' => $numeroPlantaActual,
            'id_edificio' => $idEdificio
        ], [
            'numero_planta_actual' => 'required|int',
            'id_edificio' => 'required|int|min:1'
        ]);

        // Validar datos de actualización
        $validatedData = Validator::validate($data, [
            'nuevo_numero_planta' => 'int|min:-10|max:100'
        ]);

        if (empty($validatedData)) {
            throw new ValidationException(["Se requiere al menos un campo para actualizar"]);
        }

        try {
            // Verificar que la planta existe
            if (!$this->model->exists($numeroPlantaActual, $idEdificio)) {
                throw new ValidationException(["Planta no encontrada"]);
            }

            // Actualizar la planta
            $success = $this->model->update($numeroPlantaActual, $idEdificio, $validatedData);
            
            if (!$success) {
                throw new \Exception("No se pudo actualizar la planta", 500);
            }

            // Obtener detalles actualizados
            $nuevoNumero = $validatedData['nuevo_numero_planta'] ?? $numeroPlantaActual;
            $planta = $this->model->getDetails($nuevoNumero, $idEdificio);
            
            if (!$planta) {
                throw new \Exception("Planta actualizada pero no se pudo obtener sus datos", 500);
            }

            return $planta;
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error al actualizar planta: " . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar una planta
     */
    public function deletePlanta(int $numeroPlanta, int $idEdificio): void
    {
        // Validar parámetros
        Validator::validate([
            'numero_planta' => $numeroPlanta,
            'id_edificio' => $idEdificio
        ], [
            'numero_planta' => 'required|int',
            'id_edificio' => 'required|int|min:1'
        ]);

        try {
            // Verificar que la planta existe
            if (!$this->model->exists($numeroPlanta, $idEdificio)) {
                throw new ValidationException(["Planta no encontrada"]);
            }

            // Eliminar la planta
            $success = $this->model->delete($numeroPlanta, $idEdificio);
            
            if (!$success) {
                throw new \Exception("No se pudo eliminar la planta", 500);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error al eliminar planta: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener detalles de una planta específica
     */
    public function getPlantaDetails(int $numeroPlanta, int $idEdificio): array
    {
        // Validar parámetros
        Validator::validate([
            'numero_planta' => $numeroPlanta,
            'id_edificio' => $idEdificio
        ], [
            'numero_planta' => 'required|int',
            'id_edificio' => 'required|int|min:1'
        ]);

        try {
            $planta = $this->model->getDetails($numeroPlanta, $idEdificio);
            
            if (!$planta) {
                throw new ValidationException(["Planta no encontrada"]);
            }

            return $planta;
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new \Exception("Error al obtener detalles de la planta: " . $e->getMessage(), 500);
        }
    }
}