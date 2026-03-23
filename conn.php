<?php
$host = 'localhost';
$db = 'ecommerce_app';
$user = 'ecom_user';       // Dedicated app user (required on Ubuntu/EC2)
$pass = 'StrongPass123!';  // Change this to your preferred password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log the error securely
    error_log(date('[Y-m-d H:i:s] ') . "Connection failed: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/db_errors.log');
    
    // Display a user-friendly error message
    http_response_code(500);
    die("<html><body style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h1>Service Unavailable</h1>
            <p>We are currently experiencing technical difficulties connecting to our database. Please try again later.</p>
         </body></html>");
}
?>
