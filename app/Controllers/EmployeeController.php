<?php

namespace App\Controllers;

use App\Models\Employee;

class EmployeeController
{
    public function index()
    {
        $employeeModel = new Employee();
        $employees = $employeeModel->getAll();

        require_once __DIR__ . '/../Views/employees/index.php';
    }
}
