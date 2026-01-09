<?php

class Database {
    private $dbhost = "localhost";
    private $dbuser = "root";
    private $dbpass = "";
    private $db = "packit";
    private $conn = null;

    function connect(){
        if($this->conn === null){
            $this->conn = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->db)
                or die("Connect failed: %s\n". $this->conn -> error);

            if(! $this->conn) {
                die("Connection Failed.  ". mysqli_connect_error());
            }
        }

        return $this->conn;
    }

    function executeQuery($query, $params = []){
        $conn = $this->connect();

        $stmt = mysqli_prepare($conn, $query);

        if(! $stmt){
            die("Prepare failed: " . mysqli_error($conn));
        }

        if(! empty($params)){
            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);

        return $stmt;
    }

    function lastInsertId(){
        $conn = $this->connect();
        return mysqli_insert_id($conn);
    }

    function fetch($stmt) {
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}