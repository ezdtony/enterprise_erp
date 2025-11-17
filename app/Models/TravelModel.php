<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class TravelModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $query = $this->db->query("
            SELECT tr.*, 
                   CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                   p.name AS project_name
            FROM travel_requests tr
            INNER JOIN employees e ON e.id = tr.employee_id
            INNER JOIN projects p ON p.id = tr.project_id
            ORDER BY tr.id DESC
        ");

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT tr.*, 
                   CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                   p.name AS project_name
            FROM travel_requests tr
            INNER JOIN employees e ON e.id = tr.employee_id
            INNER JOIN projects p ON p.id = tr.project_id
            WHERE tr.id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByRequest($request_id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM travel_expenses
            WHERE request_id = ?
        ");

        $stmt->execute([$request_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO travel_expenses (request_id, category, amount, description)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data["request_id"],
            $data["category"],
            $data["amount"],
            $data["description"]
        ]);
    }

    public function storeTravelRequest($data)
    {
        $stmt = $this->db->prepare("
        INSERT INTO travel_requests 
        (employee_id, project_id, purpose, amount_requested, max_pay_date, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

        return $stmt->execute([
            $data["employee_id"],
            $data["project_id"],
            $data["purpose"],
            $data["amount_requested"],
            $data["max_pay_date"],
            $data["status"]
        ]);
    }

    public function getTravelExpenses()
    {
        $sql = "SELECT 
            te.id,
            te.amount,
            te.description,
            te.log_date,
            DATE_FORMAT(te.expense_date, '%d/%m/%Y') AS formatted_date,

            -- Categoría de gasto
            ec.name AS category_name,

            -- Nombre completo del empleado
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name,

            -- Proyecto relacionado
            p.name AS project_name

        FROM travel_expenses te
        INNER JOIN expense_categories ec 
            ON te.category_id = ec.id

        INNER JOIN travel_requests tr 
            ON te.request_id = tr.id

        INNER JOIN employees e 
            ON tr.employee_id = e.id

        INNER JOIN projects p 
            ON tr.project_id = p.id

        ORDER BY te.id DESC
    ";


        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function storeExpense($data)
    {
        $stmt = $this->db->prepare("
        INSERT INTO travel_expenses 
        (request_id, category_id, employee_id, project_id, amount, expense_date, description, is_deductible, has_invoice)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
        $success = $stmt->execute([
            $data['category_id'],
            $data['category_id'],
            $data['employee_id'],
            $data['project_id'],
            $data['amount'],
            $data['expense_date'],
            $data['description'],
            $data['is_deductible'],
            $data['has_invoice']
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    public function storeExpensePhoto($expenseId, $filename)
    {
        $stmt = $this->db->prepare("
        INSERT INTO expense_photos (expense_id, file_path)
        VALUES (?, ?)
    ");

        return $stmt->execute([$expenseId, $filename]);
    }
    public function storeExpenseInvoice($expenseId, $filename)
    {
        $stmt = $this->db->prepare("
        INSERT INTO expense_invoices (expense_id, file_path)
        VALUES (?, ?)
    ");

        return $stmt->execute([$expenseId, $filename]);
    }
    public function getCategories()
    {
        $query = $this->db->query("SELECT id, name FROM expense_categories ORDER BY name ASC");
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getExpensePhotos($expenseId)
    {
        $stmt = $this->db->prepare("
        SELECT file_path 
        FROM expense_photos 
        WHERE expense_id = ?
    ");
        $stmt->execute([$expenseId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getExpenseInvoices($expenseId)
    {
        $stmt = $this->db->prepare("
        SELECT file_path 
        FROM expense_invoices 
        WHERE expense_id = ?
    ");
        $stmt->execute([$expenseId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
