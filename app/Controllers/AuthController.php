<?php

namespace App\Controllers;

use App\Models\Employee;

class AuthController
{
    private $employee;

    public function __construct()
    {
        $this->employee = new Employee();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($user, $password)
    {
        $user = $this->employee->getByEmail($user);

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
            $_SESSION['position_id'] = $user->position_id;

            if ($isAjax) {
                echo json_encode(['success' => true]);
            } else {
                header("Location: /dashboard");
            }
            exit;
        } else {
            if ($isAjax) {
                echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
            } else {
                $_SESSION['error'] = "Usuario o contraseña incorrectos";
                header("Location: /login");
            }
            exit;
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        // Si tienes definida la constante BASE_URL
        header("Location: " . BASE_URL . "/login");
        exit;
    }

    public static function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }
}
