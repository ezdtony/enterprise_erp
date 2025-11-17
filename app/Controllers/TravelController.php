<?php

namespace App\Controllers;

use App\Models\TravelModel;
use App\Models\Employee;
use App\Models\ProjectModel;

class TravelController extends BaseController
{
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

        $amount = floatval(str_replace([',', '$'], '', $_POST['amount_requested']));

        $model = new TravelModel();

        $data = [
            "employee_id" => $_POST['employee_id'],
            "project_id" => $_POST['project_id'],
            "purpose" => $_POST['purpose'],
            "amount_requested" => $amount,
            "max_pay_date" => $_POST['payDate'],
            "status" => "pending"
        ];

        $saved = $model->storeTravelRequest($data);

        echo json_encode([
            "response" => $saved,
            "message" => $saved ? "Solicitud registrada correctamente." : "Error al guardar en la base de datos."
        ]);
    }

    public function saveExpense()
    {
        // Validaciones requeridas
        $required = ['request_id', 'category_id', 'project_id', 'amount', 'expense_date', 'description'];

        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                echo json_encode([
                    "response" => false,
                    "message" => "El campo $field es obligatorio"
                ]);
                return;
            }
        }

        $amount = floatval(str_replace([',', '$'], '', $_POST['amount']));
        $model = new TravelModel();

        $data = [
            "request_id" => $_POST['request_id'],
            "category_id" => $_POST['category_id'],
            "employee_id" => $_SESSION['user_id'],
            "project_id" => $_POST['project_id'],
            "amount" => $amount,
            "expense_date" => $_POST['expense_date'],
            "description" => $_POST['description'],
            "is_deductible" => isset($_POST['is_deductible']) ? 1 : 0,
            "has_invoice" => isset($_POST['has_invoice']) ? 1 : 0,
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
        // Guardar fotos (JPG/PNG)
        // ============================================
        if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
            $allowedPhotoExt = ['jpg', 'jpeg', 'png'];
            $uploadDir = __DIR__ . '/../../public/uploads/expenses/photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
                $tmp  = $_FILES['photos']['tmp_name'][$i];
                $ext  = strtolower(pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION));
                $mime = mime_content_type($tmp);

                if (!in_array($ext, $allowedPhotoExt) || strpos($mime, 'image/') !== 0) {
                    echo json_encode([
                        "response" => false,
                        "message" => "Solo se permiten fotos JPG o PNG."
                    ]);
                    return;
                }

                $timestamp = time();
                $filename  = "expense_{$timestamp}_{$i}." . $ext;
                $dest = $uploadDir . $filename;

                if (move_uploaded_file($tmp, $dest)) {
                    $model->storeExpensePhoto($expenseId, $filename);
                }
            }
        }

        // ============================================
        // Guardar factura PDF (obligatoria)
        // ============================================
        if (isset($_POST['has_invoice']) && $_POST['has_invoice'] == 1) {
            if (!isset($_FILES['invoice_pdf']) || $_FILES['invoice_pdf']['error'] !== 0) {
                echo json_encode([
                    "response" => false,
                    "message" => "El PDF de la factura es obligatorio."
                ]);
                return;
            }

            $cfdi_code = isset($_POST['cfdi_code']) ? trim($_POST['cfdi_code']) : null;
            if (!$cfdi_code) {
                echo json_encode([
                    "response" => false,
                    "message" => "El CFDI es obligatorio si se marca que tiene factura."
                ]);
                return;
            }

            $uploadDir = __DIR__ . '/../../public/uploads/expenses/invoices/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // PDF
            $tmp = $_FILES['invoice_pdf']['tmp_name'];
            $original = $_FILES['invoice_pdf']['name'];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            $mime = mime_content_type($tmp);

            if ($ext !== 'pdf' || $mime !== 'application/pdf') {
                echo json_encode([
                    "response" => false,
                    "message" => "El archivo PDF no es válido."
                ]);
                return;
            }

            $cleanName = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $original);
            $filePath = $uploadDir . $cleanName;
            if (move_uploaded_file($tmp, $filePath)) {
                $model->storeExpenseInvoice($expenseId, $cleanName, $cfdi_code);
            }

            // XML opcional
            if (isset($_FILES['invoice_xml']) && $_FILES['invoice_xml']['error'] === 0) {
                $tmpXml = $_FILES['invoice_xml']['tmp_name'];
                $originalXml = $_FILES['invoice_xml']['name'];
                $extXml = strtolower(pathinfo($originalXml, PATHINFO_EXTENSION));
                $mimeXml = mime_content_type($tmpXml);

                if ($extXml === 'xml' && in_array($mimeXml, ['text/xml', 'application/xml'])) {
                    $cleanNameXml = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $originalXml);
                    $filePathXml = $uploadDir . $cleanNameXml;
                    if (move_uploaded_file($tmpXml, $filePathXml)) {
                        $model->storeExpenseInvoice($expenseId, $cleanNameXml, $cfdi_code);
                    }
                }
            }
        }

        // ============================================
        // Respuesta final
        // ============================================
        echo json_encode([
            "response" => true,
            "message" => "Gasto registrado correctamente."
        ]);
    }
}

// ======================================
// Manejo directo de peticiones AJAX
// ======================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new TravelController();
    $action = $_POST['action'];

    if (method_exists($controller, $action)) {
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
