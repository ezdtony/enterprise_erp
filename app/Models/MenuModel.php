<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class MenuModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /** Menús principales (parent_id IS NULL) */
    public function getMainModules()
    {
        $stmt = $this->db->prepare("
            SELECT id, name, icon, route, sort_order 
            FROM menu
            WHERE active = 1 AND parent_id IS NULL
            ORDER BY sort_order ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /** Submenús (parent_id NOT NULL) */
    public function getSubModules()
    {
        $stmt = $this->db->prepare("
            SELECT id, name, icon, route, parent_id, sort_order 
            FROM menu
            WHERE active = 1 AND parent_id IS NOT NULL
            ORDER BY parent_id ASC, sort_order ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
