<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\RolService;

class RolController
{
    private RolService $service;

    public function __construct()
    {
        $this->service = new RolService();
    }

    // Listar roles
    public function index(Request $req, Response $res): void
    {
        try {
            $roles = $this->service->getRoles();
            $res->status(200)->json($roles);
        } catch (Throwable $e) {
            $res->errorJson($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}