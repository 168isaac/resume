<?php 

require_once __DIR__ . '/../includes/app.php';

use App\Router;
use Controllers\HomeController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->checkRoutes();