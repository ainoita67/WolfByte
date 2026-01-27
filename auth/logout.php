<<<<<<< HEAD

=======
<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php'; // ajusta la ruta si cambia

use Controllers\AuthSessionController;
use Core\Request;
use Core\Response;

$request = new Request();
$response = new Response();
                
try {
    $controller = new AuthSessionController();
    $controller->logout($request, $response);
} catch (Throwable $e) {
    $response->status(500)->json([], 'Error al cerrar sesión');
}
>>>>>>> origin/API
