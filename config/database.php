<?php
// config/database.php
// Database configuration using Environment Variables

// Load Composer autoloader for dotenv
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get database credentials from environment
$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_NAME'] ?? '';
$dbUser = $_ENV['DB_USER'] ?? '';
$dbPass = $_ENV['DB_PASS'] ?? '';

// Validate that required database credentials are present
if (empty($dbName) || empty($dbUser)) {
    die('Database configuration is incomplete. Please check your .env file.');
}

// Define constants for backward compatibility
define('DB_HOST', $dbHost);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);

/**
 * Get a PDO database connection
 * 
 * @return \PDO
 * @throws \PDOException
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '" . ($_ENV['APP_TIMEZONE'] ?? '+00:00') . "'"
        ]);
        
        // Set connection timezone to match application timezone
        $timezone = $_ENV['APP_TIMEZONE'] ?? 'UTC';
        $offset = (new DateTime('now', new DateTimeZone($timezone)))->format('P');
        $pdo->exec("SET time_zone = '$offset'");
        
        return $pdo;
    } catch (PDOException $e) {
        // Log error details for debugging
        error_log("Database Connection Failed: " . $e->getMessage());
        error_log("Connection Details: Host=" . DB_HOST . ", DB=" . DB_NAME . ", User=" . DB_USER);
        
        // Show user-friendly error message
        $isDebug = $_ENV['APP_DEBUG'] ?? false;
        if ($isDebug) {
            die("Database connection failed: " . $e->getMessage());
        } else {
            die("Database connection failed. Please contact administrator.");
        }
    }
}

/**
 * Generate a secure API key for users
 * 
 * @param int $userId User ID
 * @return string Hashed API key
 */
function generateApiKey($userId) {
    $randomBytes = bin2hex(random_bytes(32));
    $timestamp = time();
    $uniqueString = $userId . $timestamp . $randomBytes;
    
    return hash('sha256', $uniqueString);
}

/**
 * Test database connection (for debugging)
 * 
 * @return bool True if connection successful
 */
function testDatabaseConnection() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT 1");
        return $stmt->fetchColumn() === '1';
    } catch (Exception $e) {
        return false;
    }
}

// Optional: Test connection on debug mode
if ($_ENV['APP_DEBUG'] ?? false) {
    if (!testDatabaseConnection()) {
        error_log("WARNING: Database connection test failed on startup");
    } else {
        error_log("INFO: Database connection successful");
    }
}
?>