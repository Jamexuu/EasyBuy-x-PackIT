<?php

require_once __DIR__ . '/Database.php';

class Vehicle
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllVehicles()
    {
        $stmt = $this->db->executeQuery("SELECT * FROM vehicles ORDER BY id ASC");
        return $this->db->fetch($stmt);
    }

    public function getVehicleById($id)
    {
        $stmt = $this->db->executeQuery("SELECT * FROM vehicles WHERE id = ? LIMIT 1", [$id]);
        $rows = $this->db->fetch($stmt);
        return !empty($rows) ? $rows[0] : null;
    }
}