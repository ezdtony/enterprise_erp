<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Employee
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $sql = "SELECT e.id, CONCAT(e.first_name, ' ', e.last_name) AS full_name,
        e.first_name, ' ', e.last_name,
                       p.name AS position, d.name AS department, e.status
                FROM employees e
                JOIN positions p ON e.position_id = p.id
                JOIN departments d ON p.department_id = d.id
                ORDER BY e.id ASC";
        $stmt = $this->db->query($sql);


        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }

        return $results;
    }
}
