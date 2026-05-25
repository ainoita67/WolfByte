<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\LogAccionesService;

class LogAccionesController
{
    private LogAccionesService $service;

    public function __construct()
    {
        $this->service = new LogAccionesService();
    }

    // Listar log
    public function index(Request $req, Response $res): void
    {
        try {
            $log = $this->service->getLog();
            $res->status(200)->json($log);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Listar log paginado
    public function indexPaginado(Request $req, Response $res): void
    {
        try {
            $data=$req->getBody() ?? [];
            $log = $this->service->getLog($data);
            $res->status(200)->json($log);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Listar tipos de log
    public function indexTipoLog(Request $req, Response $res): void
    {
        try {
            $log = $this->service->getTipoLog();
            $res->status(200)->json($log);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}