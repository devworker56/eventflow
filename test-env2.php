<?php
// test-env2.php
echo "<h3>Testing .env on Server</h3>";

// Check if .env exists
$envPath = __DIR__ . '/.env';
echo ".env path: $envPath<br>";
echo ".env exists: " . (file_exists($envPath) ? 'YES' : 'NO') . "<br>";
echo ".env readable: " . (is_readable($envPath) ? 'YES' : 'NO') . "<br>";
echo ".env size: " . (file_exists($envPath) ? filesize($envPath) : 0) . " bytes<br>";

// Try to load it
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    echo "Autoload loaded<br>";
    
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        echo ".env loaded successfully!<br>";
        
        // Test a key
        echo "STRIPE_SECRET_KEY: " . (!empty($_ENV['STRIPE_SECRET_KEY']) ? 'SET' : 'EMPTY') . "<br>";
        echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NOT FOUND') . "<br>";
        
    } catch (Exception $e) {
        echo "Error loading .env: " . $e->getMessage() . "<br>";
    }
}
?>