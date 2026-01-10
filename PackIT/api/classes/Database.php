<?php

declare(strict_types=1);

class Database
{
    // === Configure these values to match your environment ===
    private string $DB_HOST = '127.0.0.1';
    private string $DB_NAME = 'packit';
    private string $DB_USER = 'root';
    private string $DB_PASS = '';
    private int $DB_PORT = 3306;
    private string $DB_CHARSET = 'utf8mb4';
    // =======================================================

    private ?PDO $pdo = null;        // PDO connection (like $pdo in db.php)
    private ?mysqli $conn = null;    // mysqli connection (like $db in db.php)

    /**
     * Get (or create) a PDO connection.
     */
    public function pdo(): ?PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        try {
            $dsn = "mysql:host={$this->DB_HOST};port={$this->DB_PORT};dbname={$this->DB_NAME};charset={$this->DB_CHARSET}";
            $this->pdo = new PDO($dsn, $this->DB_USER, $this->DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("PDO connection failed: " . $e->getMessage());
            $this->pdo = null;
        }

        return $this->pdo;
    }

    /**
     * Get (or create) a mysqli connection.
     * Keeps backward compatibility with your existing connect() usage.
     */
    public function connect(): ?mysqli
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $mysqli = mysqli_init();
            if (!$mysqli->real_connect(
                $this->DB_HOST,
                $this->DB_USER,
                $this->DB_PASS,
                $this->DB_NAME,
                $this->DB_PORT
            )) {
                error_log('mysqli connect failed: ' . mysqli_connect_error());
                $this->conn = null;
            } else {
                $this->conn = $mysqli;
            }
        } catch (Throwable $e) {
            error_log('mysqli connect exception: ' . $e->getMessage());
            $this->conn = null;
        }

        return $this->conn;
    }

    /**
     * Execute a prepared statement using mysqli (existing behavior).
     */
    public function executeQuery(string $query, array $params = []): mysqli_stmt
    {
        $conn = $this->connect();
        if (!$conn) {
            throw new RuntimeException("Database connection failed (mysqli). Check error logs.");
        }

        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . mysqli_error($conn));
        }

        if (!empty($params)) {
            // Default: bind everything as string, same as your current code.
            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException("Execute failed: " . mysqli_stmt_error($stmt));
        }

        return $stmt;
    }

    public function lastInsertId(): int
    {
        $conn = $this->connect();
        if (!$conn) {
            throw new RuntimeException("Database connection failed (mysqli).");
        }
        return mysqli_insert_id($conn);
    }

    public function fetch(mysqli_stmt $stmt): array
    {
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {
            return [];
        }
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * Optional convenience accessors if you want parity with db.php names.
     */
    public function mysqli(): ?mysqli
    {
        return $this->connect();
    }
}