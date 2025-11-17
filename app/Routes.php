<?php

use App\Controllers\HomeController;
use App\Controllers\EmployeeController;
use App\Controllers\TravelController;


// Obtener la ruta actual (sin query params)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$uri = str_replace($scriptName, '', $_SERVER['REQUEST_URI']);
$uri = parse_url($uri, PHP_URL_PATH);
$uri = trim($uri, '/');

// Enrutador básico
switch ($uri) {
    case '':
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;

    case 'employees':
        $controller = new EmployeeController();
        $controller->index();
        break;

    case 'travel-request':
        $controller = new TravelController();
        $controller->index();
        break;

    case 'travel-request-action':
        $controller = new TravelController();
        $action = $_POST['action'] ?? null;

        if ($action && method_exists($controller, $action)) {
            $controller->{$action}();
        } else {
            echo json_encode([
                "response" => false,
                "message" => "Acción inválida."
            ]);
        }
        break;

    case 'check-expenses':
        $controller = new TravelController();
        $controller->check_expenses();
        break;

    default:
        http_response_code(404);
        echo "404 | Page not found";
        break;
}
