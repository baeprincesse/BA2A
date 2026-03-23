<?php
/**
 * init_db.php - Database & Table Initializer
 * Run once on the server: php init_db.php
 * Uses credentials from conn.php (ecom_user / StrongPass123!)
 */

$host    = 'localhost';
$db      = 'ecommerce_app';
$user    = 'ecom_user';       // Must match the MySQL user created on the server
$pass    = 'StrongPass123!';  // Must match the MySQL user password on the server
$charset = 'utf8mb4';

try {
    // Connect WITHOUT specifying a DB first so we can create it if needed
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 1. Create the database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$db' created or already exists.\n";

    // 2. Switch to the database
    $pdo->exec("USE `$db`");

    // 3. Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id         INT(11)      AUTO_INCREMENT PRIMARY KEY,
        username   VARCHAR(50)  NOT NULL UNIQUE,
        password   VARCHAR(255) NOT NULL,
        role       ENUM('admin','customer') DEFAULT 'customer',
        created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Table 'users' OK.\n";

    // 4. Default admin user
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')")
            ->execute(['admin', $hash]);
        echo "Default admin user created (username: admin / password: admin123).\n";
    } else {
        echo "Admin user already exists.\n";
    }

    // 5. Categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id         INT(11)      AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "Table 'categories' OK.\n";

    // 6. Products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id          INT(11)        AUTO_INCREMENT PRIMARY KEY,
        category_id INT(11)        NOT NULL,
        name        VARCHAR(255)   NOT NULL,
        description TEXT,
        price       DECIMAL(10,2)  NOT NULL,
        image_path  VARCHAR(255)   DEFAULT NULL,
        created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "Table 'products' OK.\n";

    // 7. Carts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS carts (
        id         INT(11)   AUTO_INCREMENT PRIMARY KEY,
        user_id    INT(11)   NOT NULL,
        product_id INT(11)   NOT NULL,
        quantity   INT(11)   NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "Table 'carts' OK.\n";

    // 8. Payments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
        id         INT(11)        AUTO_INCREMENT PRIMARY KEY,
        user_id    INT(11)        NOT NULL,
        product_id INT(11)        NOT NULL,
        amount     DECIMAL(10,2)  NOT NULL,
        status     ENUM('pending','completed','failed') DEFAULT 'pending',
        created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "Table 'payments' OK.\n";

    // 9. Uploads directory
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
        echo "Uploads directory created.\n";
    } else {
        echo "Uploads directory already exists.\n";
    }

    echo "\nDatabase initialisation complete!\n";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
