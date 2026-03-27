<?php
declare(strict_types=1);

namespace Services;

use Models\LogAccionesModel;
use Validation\Validator;
use Validation\ValidationException;
use Throwable;

class LogAccionesService
{
    private LogAccionesModel $model;

    public function __construct()
    {
        $this->model = new LogAccionesModel();
    }

    /**
     * Obtener todos los logs
     */
    public function getLog(array $data=[]): array
    {
        try {
            $totalPages=$this->model->totalpaginas($data);
            $data['perPage']=$data['perPage'] ?? $totalPages;
            $logs=$this->model->all($data);

            return [
                'data' => $logs,
                'totalPages' => $totalPages,
                'currentPage' => $data['page'] ?? 1
            ];
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener todos los logs
     */
    public function getTipoLog(): array
    {
        try {
            return $this->model->allTipoLog();
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener todos los logs
     */
    public function getTipoLogByTipo(string $tipo): array|false
    {
        try {
            return $this->model->findTipoLogByTipo($tipo);
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener todos los logs
     */
    public function createLog(string $tipo, array $input): array
    {
        try {
            $nombreTipo=Validator::validate(['tipo' => $tipo], [
                'tipo' => 'required|string|min:1'
            ]);

            $tipoLog=$this->getTipoLogByTipo($nombreTipo['tipo']);

            if (!$tipoLog) {
                throw new \Exception("Tipo de log no encontrado: " . $nombreTipo['tipo']);
            }
            
            $idTipo=Validator::validate($tipoLog, [
                'id_tipo_log'   => 'required|int|min:1',
                'tipo'          => 'required|string|min:1'
            ]);
            
            $data = Validator::validate($input, [
                'id_usuario'            => 'int|min:1',
                'id_incidencia'         => 'int|min:1',
                'id_reserva'            => 'int|min:1',
                'id_recurso'            => 'string|min:1',
                'id_reserva_permanente' => 'int|min:1',
                'id_liberacion_puntual' => 'int|min:1',
                'id_usuario_actor'      => 'required|int|min:1'
            ]);
            
            return $this->model->findById($this->model->create((int)$idTipo['id_tipo_log'], $data));
        } catch (Throwable $e) {
            throw new \Exception("Error interno en la base de datos: " . $e->getMessage(), 500);
        }
    }
}