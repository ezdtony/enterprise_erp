<?php

use App\Controllers\HomeController;
use App\Controllers\EmployeeController;
use App\Controllers\TravelController;
use App\Controllers\AuthController;

$auth = new AuthController();



// Obtener la ruta actual (sin query params)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$uri = str_replace($scriptName, '', $_SERVER['REQUEST_URI']);
$uri = parse_url($uri, PHP_URL_PATH);
$uri = trim($uri, '/');


$protected_routes = ['home', 'employees', 'travel-request', 'check-expenses'];

if (in_array($uri, $protected_routes)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/login");
        exit;
    }
}

// Enrutador básico
switch ($uri) {
    case '':
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar login
            $auth->login($_POST['user'], $_POST['password']);
        } else {
            // Mostrar formulario de login
            require_once __DIR__ . '/../app/Views/login.php';
        }
        break;

    case 'logout':
        $auth->logout();
        break;

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
