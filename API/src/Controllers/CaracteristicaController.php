<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Validation\ValidationException;
use Throwable;
use Services\CaracteristicaService;

class CaracteristicaController{

    private CaracteristicaService $service;

    public function __construct()
    {
        $this->service = new CaracteristicaService();
    }

    

}