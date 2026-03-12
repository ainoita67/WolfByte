<?php
declare(strict_types=1);

namespace Services;

use Core\Request;
use Core\Response;
use Models\LiberacionPuntualModel;
use Throwable;
use Validation\Validator;
use Validation\ValidationException;

class LiberacionPuntualService
{
    private LiberacionPuntualModel $model;

    public function __construct()
    {
        $this->model = new LiberacionPuntualModel();
    }

    /**
     * Obtener todas las liberaciones puntuales activas
     */
    public function getAllLiberacionesPuntuales(): array
    {
        return $this->model->getAll();
    }

    /**
     * Obtener liberación puntual por ID
     */
    public function getLiberacionPuntualById(string $id): array
    {
        $data = Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        try{
            $liberacion = $this->model->findById($data['id']);
            return $liberacion;
        }catch(Throwable $e) {
            throw new ValidationException(["Liberación puntual no encontrada"]);
        }

        return ["mensaje" => "Liberación puntual no encontrada"];
    }

    /**
     * Obtener liberación puntual por ID de recurso
     */
    public function getLiberacionPuntualByIdRecurso(string $id_recurso): array
    {
        $data = Validator::validate(['id_recurso' => $id_recurso], [
            'id_recurso' => 'required|string|min:1'
        ]);

        try{
            $liberacion = $this->model->findByIdRecurso($data['id_recurso']);
            return $liberacion;
        }catch(Throwable $e) {
            throw new ValidationException(["Liberación puntual no encontrada"]);
        }

        return ["mensaje" => "Liberación puntual no encontrada"];
    }

    /**
     * Obtener liberación puntual por ID de usuario
     */
    public function getLiberacionPuntualByIdUsuario(string $id_usuario): array
    {
        $data = Validator::validate(['id_usuario' => $id_usuario], [
            'id_usuario' => 'required|int|min:1'
        ]);

        try{
            $liberacion = $this->model->findByIdUsuario($data['id_usuario']);
            return $liberacion;
        }catch(Throwable $e) {
            throw new ValidationException(["Liberación puntual no encontrada"]);
        }

        return ["mensaje" => "Liberación puntual no encontrada"];
    }

    /**
     * Crear liberación puntual
     */
    public function createLiberacionPuntual(array $input): array
    {

        $data = Validator::validate($input, [
            'inicio'                => 'required|string',
            'fin'                   => 'required|string',
            'comentario'            => 'string',
            'id_reserva'            => 'int',
            'id_reserva_permanente' => 'required|int',
        ]);
        
        if (empty($data['id_reserva_permanente'])) {
            throw new ValidationException(array("id_reserva_permanente es obligatorio"));
        }

        return $this->model->create($data);
    }

    /**
     * Crear liberación puntual por ID de reserva
     */
    public function createLiberacionPuntualByReserva(string $id_reserva, array $input): array
    {
        $id_r = Validator::validate(['id_reserva' => $id_reserva], [
            'id_reserva' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'inicio'                => 'required|string',
            'fin'                   => 'required|string',
            'comentario'            => 'string',
            'id_reserva_permanente' => 'required|int',
        ]);
        
        if (empty($id_r['id_reserva']) || empty($data['id_reserva_permanente'])) {
            throw new ValidationException(array("id_reserva e id_reserva_permanente son obligatorios"));
        }

        return $this->model->createByReserva((int)$id_r['id_reserva'], $data);
    }

    /**
     * Actualizar liberación puntual
     */
    public function updateLiberacionPuntual(string $id_liberacion, array $input): array
    {
        $id = Validator::validate(['id' => $id_liberacion], [
            'id' => 'required|int|min:1'
        ]);

        $data = Validator::validate($input, [
            'inicio'                => 'required|string',
            'fin'                   => 'required|string',
            'comentario'            => 'required|string',
            'id_reserva'            => 'required|int|min:1',
            'id_reserva_permanente' => 'required|int|min:1',
        ]);

        if (empty($data['id_reserva']) && empty($data['id_reserva_permanente'])) {
            throw new ValidationException("id_reserva e id_reserva_permanente son obligatorios");
        }

        return $this->model->update((int)$id['id'], $data);
    }

    /**
     * Eliminar una liberación puntual por ID
     */
    public function deleteLiberacionPuntual(string $id): array
    {
        $data = Validator::validate(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);

        if(empty($data['id'])) {
            throw new ValidationException("ID es obligatorio");
        }

        return $this->model->delete($data['id']);
    }
}