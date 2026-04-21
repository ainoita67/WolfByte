<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\UsuarioService;
use Services\LogAccionesService;

class UsuarioController
{
    private UsuarioService $service;
    private LogAccionesService $serviceLog;

    public function __construct()
    {
        $this->service = new UsuarioService();
        $this->serviceLog = new LogAccionesService();
    }

    // Listar usuarios activos
    public function index(Request $req, Response $res): void
    {
        try {
            $usuarios = $this->service->getAllUsuarios();
            $res->status(200)->json($usuarios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Listar usuarios inactivos
    public function indexin(Request $req, Response $res): void
    {
        try {
            $usuarios = $this->service->getInactiveUsuarios();
            $res->status(200)->json($usuarios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Mostrar usuario por ID
    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $usuario = $this->service->getUsuarioById((int)$id);
            $res->status(200)->json($usuario);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Crear usuario
    public function store(Request $req, Response $res): void
    {
        try {
            $data=$req->json();
            $log['id_usuario_actor']=$data['id_usuario_actor'];
            $usuario = $this->service->createUsuario($data);
            $log['id_usuario']=$usuario['id'];
            $this->serviceLog->createLog('Creación de usuario', $log);
            $res->status(201)->json(['id' => $usuario['id']], "Usuario creado correctamente");
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * POST /user/importar
     * Importa usuarios desde un CSV
     */
    public function importar(Request $req, Response $res): void
    {
        try {
            if (!isset($_FILES['archivo'])) {
                throw new ValidationException("No se ha enviado ningún archivo");
            }
            if (!isset($_POST['id_usuario'])||!isset($_POST['correo_usuario'])) {
                throw new ValidationException("Error al obtener el usuario");
            }

            $file = $_FILES['archivo'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new ValidationException("Error al subir el archivo");
            }

            $tmpPath = $file['tmp_name'];

            // Abrir CSV
            if (($handle = fopen($tmpPath, 'r')) === false) {
                throw new ValidationException("No se puede leer el CSV");
            }
            
            $firstLine = fgetcsv($handle);

            if ($firstLine === false) {
                throw new ValidationException("CSV vacío o inválido");
            }

            $delimiter = str_contains(implode(',', $firstLine), ';') ? ';' : ',';
            rewind($handle);

            $header = array_map('trim', fgetcsv($handle, 0, $delimiter));
            $resultados = [];

            $dias = [
                'lunes' => 1,
                'martes' => 2,
                'miercoles' => 3,
                'miércoles' => 3,
                'jueves' => 4,
                'viernes' => 5,
            ];

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {

                if (count($row) !== count($header)) {
                    continue;
                }

                $row = array_combine($header, $row);

                if ($row === false) {
                    continue;
                }

                $diasemana=trim($row['dia_semana'] ?? '');
                if (is_numeric($diasemana)) {
                    $dia = (int)$diasemana;
                } else {
                    $dia = strtolower($diasemana);
                    $dia = $dias[$dia] ?? null;
                }

                $data = [
                    'dia_semana' => $dia ?? null,
                    'inicio'     => $row['inicio'] ?? null,
                    'fin'        => $row['fin'] ?? null,
                    'comentario' => $row['comentario'] ?? null,
                    'id_recurso' => $row['id_recurso'] ?? null,
                    'unidades'   => !empty($row['unidades'] ?? null) ? (int)$row['unidades'] : null,
                    'activo'     => 1
                ];

                $data['activo']=1;

                $reserva = $this->service->createReservaPermanente($data);
                $log['id_reserva_permanente']=$reserva['id_reserva_permanente'];
                $resultados[] = $reserva;
                $log['id_usuario_actor'] = $_POST['id_usuario'];
                $this->serviceLog->createLog("Creación de reserva permanente", $log);
            }

            fclose($handle);
            $this->serviceMail->createMail($_POST['correo_usuario'], 'importar');

            $res->status(201)->json([
                "importadas" => count($resultados),
                "datos" => $resultados
            ]);
            $res->status(201)->json($reserva);
        } catch (ValidationException $e) {
            $res->errorJson($e->getMessage(), 422);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), 500);
        }
    }


    // Actualizar usuario
    public function update(Request $req, Response $res, string $id): void
    {
        try {
            $data=$req->json();
            $log['id_usuario_actor']=$data['id_usuario_actor'];
            $usuario = $this->service->updateUsuario((int)$id, $data);
            $log['id_usuario']=$id;
            if($usuario['status']!='no_changes'){
                $this->serviceLog->createLog('Modificación de usuario', $log);
            }
            $res->status(200)->json([], $usuario['message']);
        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function patch(Request $req, Response $res, string $id): void
    {
        try {
            $data = $req->json(); // Puede ser null o []
            $log['id_usuario_actor']=$data['id_usuario_actor'];

            // Si se envía contraseña → actualizar
            if (isset($data['password']) && trim($data['password']) !== '') {
                $usuario = $this->service->updatePassword((int)$id, $data['password']);
                $log['id_usuario']=$id;
                $this->serviceLog->createLog('Modificación de usuario', $log);
                $res->status(200)->json([], $usuario['message']);
                return;
            }

            // Si no se envía contraseña → activar / desactivar
            $usuario = $this->service->toggleActiveStatus((int)$id);
            $log['id_usuario']=$id;
            if($this->service->getUsuarioById((int)$id)['usuario_activo']){
                $this->serviceLog->createLog('Activación de usuario', $log);
            }else{
                $this->serviceLog->createLog('Desactivación de usuario', $log);
            }
            $res->status(200)->json([], $usuario['message']);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors]);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
