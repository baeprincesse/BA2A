#!/bin/bash
# =============================================================
# setup.sh - One-command EC2 deployment script
# Run once after pulling code to the server:
#   chmod +x setup.sh && ./setup.sh
# =============================================================

set -e  # Exit immediately on any error

echo "======================================"
echo " LuxeStore - EC2 Setup Script"
echo "======================================"

# 1. Install required packages
echo "[1/5] Installing PHP, MySQL, Node.js, PM2..."
sudo apt-get update -qq
sudo apt-get install -y php-cli php-mysql mariadb-server nodejs npm -qq
sudo npm install -g pm2 --silent

# 2. Secure MySQL and create the app user
echo "[2/5] Configuring MySQL..."
sudo mysql -e "
    CREATE DATABASE IF NOT EXISTS ecommerce_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    CREATE USER IF NOT EXISTS 'ecom_user'@'localhost' IDENTIFIED BY 'StrongPass123!';
    GRANT ALL PRIVILEGES ON ecommerce_app.* TO 'ecom_user'@'localhost';
    FLUSH PRIVILEGES;
"
echo "MySQL user 'ecom_user' configured."

# 3. Run database initialiser
echo "[3/5] Initialising database tables..."
php init_db.php

# 4. Seed sample data
echo "[4/5] Seeding sample products..."
php seed.php

# 5. Start the app with PM2
echo "[5/5] Starting the app with PM2..."
pm2 delete ecommerce_app 2>/dev/null || true
pm2 start /usr/bin/php --name "ecommerce_app" --interpreter none -- -S 0.0.0.0:8000 -t .
pm2 save
pm2 startup | tail -1 | sudo bash || true

echo ""
echo "======================================"
echo " Setup complete!"
echo " Your app is running at: http://$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4):8000"
echo " Admin login -> username: admin | password: admin123"
echo "======================================"
