<?php

require_once __DIR__ . '/../config/database.php';

class HealthController
{
    /**
     * Health check endpoint.
     * GET /health
     *
     * Verifies database connectivity with a 5-second timeout.
     * Returns HTTP 200 on success, HTTP 503 on failure.
     * Never exposes credentials, connection strings, or stack traces.
     */
    public function check(): void
    {
        header('Content-Type: application/json');

        try {
            $pdo = getDBConnection();
            $pdo->setAttribute(PDO::ATTR_TIMEOUT, 5);
            $pdo->query('SELECT 1');

            http_response_code(200);
            echo json_encode(['status' => 'ok']);
        } catch (\Exception $e) {
            http_response_code(503);
            echo json_encode([
                'status' => 'error',
                'message' => 'Service unavailable',
            ]);
        }
    }
}
