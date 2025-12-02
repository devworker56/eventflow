<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u834808878_eventflow_db');
define('DB_USER', 'u834808878_eventflow_adm');
define('DB_PASS', 'Ossouka@1968');

// Create connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Generate API key for user
function generateApiKey($userId) {
    return hash('sha256', $userId . time() . bin2hex(random_bytes(16)));
}
?>