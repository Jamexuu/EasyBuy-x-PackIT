<?php
/**
 * SMSGateway: Handles SMS requests to the Android SMS Gateway.
 *
 * Uses PHP stream_context + file_get_contents.
 * - Proper header formatting (\r\n)
 * - HTTP status validation (2xx = success)
 * - Better error messages (including HTTP response line)
 */
class SMSGateway {
    private string $gateway_url;
    private string $username;
    private string $password;

    public function __construct(
        ?string $gatewayUrl = null,
        ?string $username = null,
        ?string $password = null
    ) {
        // You can later replace these with env/config values.
        $this->gateway_url = $gatewayUrl ?? "http://192.168.1.7:8080";
        $this->username    = $username ?? "sms";
        $this->password    = $password ?? "88888888";
    }

    public function sendSMS(string $recipient, string $message): array {
        $recipient = trim($recipient);
        $message   = (string)$message;

        if ($recipient === '') {
            return ['success' => false, 'error' => 'Empty recipient number'];
        }

        if ($message === '') {
            return ['success' => false, 'error' => 'Empty message'];
        }

        $url = rtrim($this->gateway_url, '/') . '/messages';

        $payload = [
            "phoneNumbers" => [$recipient],
            "message"      => $message,
        ];

        $headers =
            "Content-Type: application/json\r\n" .
            "Authorization: Basic " . base64_encode($this->username . ":" . $this->password) . "\r\n";

        $options = [
            'http' => [
                'method'        => 'POST',
                'header'        => $headers,
                'content'       => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout'       => 30,
                'ignore_errors' => true, // allow reading response body even on 4xx/5xx
            ],
        ];

        $context = stream_context_create($options);

        $responseBody = @file_get_contents($url, false, $context);

        // file_get_contents returns false on network/connection errors
        if ($responseBody === false) {
            $lastError = error_get_last();
            return [
                'success' => false,
                'error'   => 'Cannot reach SMS Gateway' . (!empty($lastError['message']) ? (": " . $lastError['message']) : ''),
            ];
        }

        // Determine HTTP status line
        $statusLine = $http_response_header[0] ?? '';
        $statusCode = 0;

        if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $statusLine, $m)) {
            $statusCode = (int)$m[1];
        }

        $decoded = json_decode($responseBody, true);
        $jsonOk  = (json_last_error() === JSON_ERROR_NONE);

        // Treat non-2xx as failure
        if ($statusCode < 200 || $statusCode >= 300) {
            return [
                'success'     => false,
                'error'       => 'SMS Gateway HTTP error: ' . ($statusLine ?: 'Unknown status'),
                'status_code' => $statusCode,
                'response'    => $jsonOk ? $decoded : $responseBody,
            ];
        }

        return [
            'success'     => true,
            'status_code' => $statusCode,
            'response'    => $jsonOk ? $decoded : $responseBody,
        ];
    }
}
?>