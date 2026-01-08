<?php
require_once __DIR__ . '/SMSGateway.php';
require_once __DIR__ . '/SmsLogRepository.php';

class SmsNotificationService {
    private SMSGateway $sms;
    private SmsLogRepository $logRepo;

    public function __construct(
        ?SMSGateway $gateway = null,
        ?SmsLogRepository $logRepo = null
    ) {
        $this->sms = $gateway ?? new SMSGateway();
        $this->logRepo = $logRepo ?? new SmsLogRepository();
    }

    // Basic PH normalization (adjust if your format differs)
    public function normalizeNumber(string $number): string {
        $n = trim($number);
        $n = preg_replace('/[\s\-\(\)]+/', '', $n);

        if ($n === '') return '';

        if (preg_match('/^09\d{9}$/', $n)) {
            return '+63' . substr($n, 1);
        }
        if (preg_match('/^639\d{9}$/', $n)) {
            return '+' . $n;
        }
        if (preg_match('/^\+639\d{9}$/', $n)) {
            return $n;
        }

        return $n;
    }

    /**
     * @param array $numbers list of numbers
     * @param string $template message content
     * @param array $meta optional: ['booking_id'=>int, 'driver_id'=>int, 'status'=>'booking_accepted']
     */
    public function notify(array $numbers, string $template, array $meta = []): void {
        $template = trim($template);
        if ($template === '') return;

        $bookingId = isset($meta['booking_id']) ? (int)$meta['booking_id'] : null;
        $driverId  = isset($meta['driver_id']) ? (int)$meta['driver_id'] : null;
        $status    = isset($meta['status']) ? (string)$meta['status'] : 'unknown';

        $recipients = [];
        foreach ($numbers as $num) {
            $normalized = $this->normalizeNumber((string)$num);
            if ($normalized !== '') $recipients[] = $normalized;
        }
        $recipients = array_values(array_unique($recipients));

        foreach ($recipients as $number) {
            $result = $this->sms->sendSMS($number, $template);

            $ok = isset($result['success']) && $result['success'] === true;
            $err = $ok ? null : ($result['error'] ?? 'Unknown error');

            // Log to DB
            $this->logRepo->create(
                $bookingId,
                $driverId,
                $status,
                $number,
                $template,
                $ok,
                $err
            );

            // Also log to PHP error log if failed
            if (!$ok) {
                error_log("Failed to send SMS to {$number}: {$err}");
            }
        }
    }

    public function getTemplate(string $status, array $details): string {
        $bookingId  = $details['booking_id'] ?? '';
        $driverName = $details['driver_name'] ?? '';

        switch ($status) {
            case 'booking_accepted':
                return "Booking ID #{$bookingId}\nAccepted by: {$driverName}\n\nYour package has been accepted!";
            case 'picked_up':
                return "Booking ID #{$bookingId}\nPicked Up by: {$driverName}\n\nYour package is now on its way.";
            case 'in_transit':
                return "Booking ID #{$bookingId}\n\nYour package is in transit.";
            case 'delivered':
                return "Booking ID #{$bookingId}\n\nYour package has been delivered!";
            default:
                return "Your package status has been updated.";
        }
    }
}
?>