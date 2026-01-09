<?php
require_once __DIR__ . '/../classes/Database.php';

class SmsLogRepository {
    private Database $db;

    public function __construct(?Database $db = null) {
        $this->db = $db ?? new Database();
    }

    /**
     * Insert one SMS attempt row into SmsLogs.
     */
    public function create(
        ?int $bookingId,
        ?int $driverId,
        string $status,
        string $recipientNumber,
        string $message,
        bool $isSent,
        ?string $errorMessage = null
    ): void {
        $this->db->executeQuery(
            "INSERT INTO SmsLogs (BookingId, DriverId, Status, RecipientNumber, Message, IsSent, ErrorMessage)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $bookingId,
                $driverId,
                $status,
                $recipientNumber,
                $message,
                $isSent ? 1 : 0,
                $errorMessage
            ]
        );
    }
}
?>