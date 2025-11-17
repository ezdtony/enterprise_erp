<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ProjectModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("
            SELECT prj.*
            FROM erp_system.projects AS prj
            ORDER BY prj.name
        ");

        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }

        return $results;
    }
}
