<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\UsuarioService;
use Services\LogAccionesService;
use Services\MailService;

class UsuarioController
{
    private UsuarioService $service;
    private LogAccionesService $serviceLog;
    private MailService $serviceMail;

    public function __construct()
    {
        $this->service = new UsuarioService();
        $this->serviceLog = new LogAccionesService();
        $this->serviceMail = new MailService();
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
            
            $log['id_usuario_actor']=$_POST['id_usuario'];

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

            $rol = [
                'extra' => 10,
                'extraescolar' => 10,
                'comun' => 20,
                'común' => 20,
                'admin' => 30,
                'administrador' => 30,
                'superadmin' => 40,
                'superadministrador' => 40
            ];

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {

                if (count($row) !== count($header)) {
                    continue;
                }

                $row = array_combine($header, $row);

                if ($row === false) {
                    continue;
                }

                $id_rol=trim($row['id_rol'] ?? $row['rol'] ?? '');
                if (is_numeric($id_rol)) {
                    $rol = strtolower($id_rol);
                    $rol = (int)$id_rol;
                } else {
                    $rol = $id_rol[$rol] ?? null;
                }

                $data = [
                    'id_rol'        => $rol ?? null,
                    'correo'        => $row['correo'] ?? null,
                    'nombre'        => $row['nombre'] ?? null,
                    'contrasena'    => $row['contrasenya'] ?? $row['contrasena'] ?? null,
                    'token'         => $token['correo'] ?? null,
                    'expira_token'  => $expira_token['correo'] ?? null
                ];

                $data['usuario_activo']=1;

                $usuario = $this->service->createUsuario($data);
                $log['id_usuario']=$usuario['id'];
                $resultados[] = $usuario;
                $this->serviceLog->createLog('Creación de usuario', $log);
            }

            fclose($handle);

            if(count($resultados)>0){
                $this->serviceMail->createMail($_POST['correo_usuario'], 'usuarios');
            }

            $res->status(201)->json([
                "importados" => count($resultados),
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
            $res->status(200)->json($usuario);
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
