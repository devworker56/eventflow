<?php
// debug.php - Comprehensive debug script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>EventFlow Debug Diagnostic</h2>";
echo "<hr>";

// 1. Basic server info
echo "<h3>1. Server Information</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "<br>";
echo "Current Dir: " . __DIR__ . "<br>";

// 2. Check .htaccess
echo "<h3>2. .htaccess Check</h3>";
$htaccess = '.htaccess';
echo ".htaccess exists: " . (file_exists($htaccess) ? 'YES' : 'NO') . "<br>";
if (file_exists($htaccess)) {
    echo ".htaccess size: " . filesize($htaccess) . " bytes<br>";
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($htaccess)) . "<br>";
}

// 3. Check critical files
echo "<h3>3. Critical Files Check</h3>";
$files = [
    '.env',
    'config/database.php',
    'config/stripe-config.php',
    'vendor/autoload.php',
    'index.php'
];

foreach ($files as $file) {
    $exists = file_exists($file);
    $readable = is_readable($file);
    echo "$file: " . ($exists ? 'EXISTS' : 'MISSING') . 
         " / " . ($readable ? 'READABLE' : 'NOT READABLE') . "<br>";
}

// 4. Test .env loading
echo "<h3>4. Environment Test</h3>";
if (file_exists('vendor/autoload.php')) {
    try {
        require_once 'vendor/autoload.php';
        echo "Autoload loaded successfully<br>";
        
        if (file_exists('.env')) {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            echo ".env loaded successfully<br>";
            
            // Check specific keys
            $keys = ['STRIPE_SECRET_KEY', 'STRIPE_PUBLISHABLE_KEY', 'DB_HOST', 'DB_NAME'];
            foreach ($keys as $key) {
                $value = $_ENV[$key] ?? '';
                echo "$key: " . (empty($value) ? 'EMPTY' : 'SET') . "<br>";
            }
        } else {
            echo ".env file not found<br>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "vendor/autoload.php not found - Composer dependencies missing<br>";
}

// 5. Test database connection
echo "<h3>5. Database Connection Test</h3>";
if (file_exists('config/database.php')) {
    try {
        require_once 'config/database.php';
        echo "database.php loaded<br>";
        
        // Try to get connection
        $pdo = getDBConnection();
        echo "Database connection: SUCCESS<br>";
        
        // Test query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "Database query: " . ($result['test'] == 1 ? 'SUCCESS' : 'FAILED') . "<br>";
    } catch (Exception $e) {
        echo "Database error: " . $e->getMessage() . "<br>";
    }
}

// 6. Test Stripe config
echo "<h3>6. Stripe Configuration Test</h3>";
if (file_exists('config/stripe-config.php')) {
    try {
        require_once 'config/stripe-config.php';
        echo "stripe-config.php loaded<br>";
        
        if (defined('STRIPE_SECRET_KEY')) {
            echo "STRIPE_SECRET_KEY: " . (empty(STRIPE_SECRET_KEY) ? 'EMPTY' : substr(STRIPE_SECRET_KEY, 0, 8) . '...') . "<br>";
        } else {
            echo "STRIPE_SECRET_KEY not defined<br>";
        }
        
        if (defined('STRIPE_PUBLISHABLE_KEY')) {
            echo "STRIPE_PUBLISHABLE_KEY: " . (empty(STRIPE_PUBLISHABLE_KEY) ? 'EMPTY' : substr(STRIPE_PUBLISHABLE_KEY, 0, 8) . '...') . "<br>";
        } else {
            echo "STRIPE_PUBLISHABLE_KEY not defined<br>";
        }
        
        // Test Stripe class exists
        if (class_exists('Stripe\Stripe')) {
            echo "Stripe PHP library: LOADED<br>";
        } else {
            echo "Stripe PHP library: NOT FOUND<br>";
        }
    } catch (Exception $e) {
        echo "Stripe config error: " . $e->getMessage() . "<br>";
    }
}

// 7. PHP error log location
echo "<h3>7. PHP Configuration</h3>";
echo "error_log: " . ini_get('error_log') . "<br>";
echo "display_errors: " . ini_get('display_errors') . "<br>";
echo "log_errors: " . ini_get('log_errors') . "<br>";

// 8. Check for any output before this script
if (headers_sent($filename, $linenum)) {
    echo "<h3>8. Warning: Headers Already Sent</h3>";
    echo "Headers sent by: $filename on line $linenum<br>";
}

echo "<hr><h3>End of Diagnostic</h3>";
?>