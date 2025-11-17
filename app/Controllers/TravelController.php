<?php

namespace App\Controllers;

use App\Models\TravelModel;
use App\Models\Employee;
use App\Models\ProjectModel;

class TravelController  extends BaseController
{

    /* =========================================================
   MANEJO DIRECTO DE PETICIONES AJAX SIN ROUTER
   ========================================================= */

    public function index()
    {
        $model = new TravelModel();
        $employeesModel = new Employee();
        $projectModel = new ProjectModel();

        $requests = $model->getAll();
        $getEmployees = $employeesModel->getAll();
        $getProjects = $projectModel->getAll();

        require_once __DIR__ . '/../Views/travel/index.php';
    }
    public function check_expenses()
    {
        $model = new TravelModel();
        $employeesModel = new Employee();
        $projectModel = new ProjectModel();

        $requests = $model->getAll();
        $expenses = $model->getTravelExpenses();
        $categories = $model->getCategories();
        $employees = $employeesModel->getAll();
        $projects = $projectModel->getAll();

        require_once __DIR__ . '/../Views/travel/check_expenses.php';
    }

    public function saveTravelRequest()
    {
        $required = ['employee_id', 'project_id', 'purpose', 'amount_requested', 'payDate'];

        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                echo json_encode([
                    "response" => false,
                    "message" => "El campo $field es obligatorio"
                ]);
                return;
            }
        }

        $rawAmount = $_POST['amount_requested'];

        $amount = floatval(
            str_replace([',', '$'], '', $rawAmount)
        );
        // ================================
        // GUARDAR EN BD
        // ================================
        $model = new TravelModel();

        $data = [
            "employee_id"      => $_POST['employee_id'],
            "project_id"       => $_POST['project_id'],
            "purpose"          => $_POST['purpose'],
            "amount_requested" => $amount,
            "max_pay_date"       => $_POST['payDate'],
            "status"           => "pending"
        ];

        $saved = $model->storeTravelRequest($data);

        echo json_encode([
            "response" => $saved,
            "message" => $saved
                ? "Solicitud registrada correctamente."
                : "Error al guardar en la base de datos."
        ]);
    }

    public function saveExpense()
    {
        // Validaciones requeridas
        $required = ['category_id', 'employee_id', 'project_id', 'amount', 'expense_date', 'description'];

        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                echo json_encode([
                    "response" => false,
                    "message" => "El campo $field es obligatorio"
                ]);
                return;
            }
        }

        // Normalizar monto
        $amount = floatval(str_replace([',', '$'], '', $_POST['amount']));

        // ============================================
        // GUARDAR GASTO PRINCIPAL
        // ============================================
        $model = new TravelModel();

        $data = [
            "category_id"    => $_POST['category_id'],
            "employee_id"    => $_POST['employee_id'],
            "project_id"     => $_POST['project_id'],
            "amount"         => $amount,
            "expense_date"   => $_POST['expense_date'],
            "description"    => $_POST['description'],
            "is_deductible"  => isset($_POST['is_deductible']) ? 1 : 0,
            "has_invoice"    => isset($_POST['has_invoice']) ? 1 : 0,
        ];

        $expenseId = $model->storeExpense($data);

        if (!$expenseId) {
            echo json_encode([
                "response" => false,
                "message" => "Error al guardar el gasto."
            ]);
            return;
        }

        // ============================================
        // GUARDAR FOTOS (JPG/PNG)
        // ============================================
        if (isset($_FILES['photos']) && $_FILES['photos']['error'][0] === 0) {

            $uploadDir = __DIR__ . '/../../public/uploads/expenses/photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {

                $tmp  = $_FILES['photos']['tmp_name'][$i];
                $ext  = pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION);

                $timestamp = time();
                $filename  = "expense_{$timestamp}_{$i}." . strtolower($ext);

                $dest = $uploadDir . $filename;

                if (move_uploaded_file($tmp, $dest)) {
                    $model->storeExpensePhoto($expenseId, $filename);
                }
            }
        }

        // ============================================
        // GUARDAR FACTURA (PDF/XML, mantener nombre)
        // ============================================
        if (isset($_POST['has_invoice']) && isset($_FILES['invoice_file'])) {

            if ($_FILES['invoice_file']['error'] === 0) {

                $uploadDir = __DIR__ . '/../../public/uploads/expenses/invoices/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $tmp = $_FILES['invoice_file']['tmp_name'];

                $original = $_FILES['invoice_file']['name'];
                $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));

                // Validar que sea pdf/xml
                if ($ext === "pdf" || $ext === "xml") {

                    // Sanitizar nombre
                    $cleanName = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $original);
                    $filename = $cleanName;
                } else {
                    // Si por alguna razón suben imagen en "factura", usar genérico
                    $filename = "invoice_" . time() . "." . $ext;
                }

                $dest = $uploadDir . $filename;

                if (move_uploaded_file($tmp, $dest)) {
                    $model->storeExpenseInvoice($expenseId, $filename);
                }
            }
        }

        // ============================================
        // RESPUESTA FINAL
        // ============================================
        echo json_encode([
            "response" => true,
            "message" => "Gasto registrado correctamente."
        ]);
    }



    public function createForm()
    {
        require_once __DIR__ . '/../Views/viaticos/modals/create_request.php';
    }

    public function show($id)
    {
        $model = new TravelModel();

        $request = $model->getById($id);
        $expenses = $model->getByRequest($id);
        $approval = $model->getByRequest($id);

        require_once __DIR__ . '/../Views/viaticos/modals/show_request.php';
    }

    public function expensesForm($id)
    {
        require_once __DIR__ . '/../Views/viaticos/modals/register_expense.php';
    }

    public function approvalForm($id)
    {
        require_once __DIR__ . '/../Views/viaticos/modals/approve_request.php';
    }

    // ============================
    // CRUD HANDLERS (POST)
    // ============================

    public function store()
    {
        $model = new TravelModel();

        $data = [
            "employee_id" => $_POST['employee_id'],
            "project_id" => $_POST['project_id'],
            "destination" => $_POST['destination'],
            "start_date" => $_POST['start_date'],
            "end_date" => $_POST['end_date'],
            "justification" => $_POST['justification']
        ];

        $model->create($data);

        header("Location: " . BASE_URL . "viaticos");
    }

    public function storeExpense()
    {
        $model = new TravelModel();

        $data = [
            "request_id" => $_POST["request_id"],
            "category" => $_POST["category"],
            "amount" => $_POST["amount"],
            "description" => $_POST["description"]
        ];

        $model->create($data);

        header("Location: " . BASE_URL . "viaticos");
    }

    public function storeApproval()
    {
        $model = new TravelModel();

        $data = [
            "request_id" => $_POST["request_id"],
            "approved_by" => $_POST["approved_by"],
            "status" => $_POST["status"],
            "notes" => $_POST["notes"]
        ];

        $model->create($data);

        header("Location: " . BASE_URL . "viaticos");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $controller = new TravelController();
    $action = $_POST['action'];

    if (method_exists($controller, $action)) {

        // Llamar al método que pidió el AJAX
        $controller->{$action}();
        exit;
    } else {

        echo json_encode([
            "response" => false,
            "message" => "La acción '$action' no existe en TravelController."
        ]);
        exit;
    }
}
