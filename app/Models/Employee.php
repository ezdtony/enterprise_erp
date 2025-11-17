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

    public function getByEmail(string $user)
    {
        $sql = "SELECT * FROM employees WHERE email = :user OR code = :user1 AND status = 'active' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user', $user, PDO::PARAM_STR);
        $stmt->bindParam(':user1', $user, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
