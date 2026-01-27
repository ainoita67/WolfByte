<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\EspacioService;

class EspacioController{
 private EspacioService $service;

    public function __construct()
    {
        $this->service = new EspacioService();
    }

    public function index(Request $req, Response $res): void
    {
        try {
            $espacios = $this->service->getAllEspacios();
            $res->status(200)->json($espacios);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(Request $req, Response $res, string $id): void
    {
        try {
            $espacio = $this->service->getEspacioById((string) $id);
            $res->status(200)->json($espacio);

        } catch (ValidationException $e) {
            $res->status(422)->json(['errors' => $e->errors], "Errores de validación");
            return;
        } catch (Throwable $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            $res->errorJson($e->getMessage(), $status);
        }
    }
}